<?php

namespace App\Http\Controllers\Api\General;

use App\DTOs\GatewayResult;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Services\General\CartInventoryService;
use App\Services\General\OrderService;
use App\Services\Gateway\CashierQrService;
use App\Services\Payment\PaymentCheckoutHandler;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Transaction;
use Marvel\Database\Models\User;
use Marvel\Enums\ShippingMethod;
use Marvel\Events\OrderCancelled;
use Marvel\Events\PaymentFailed;
use Marvel\Events\PaymentSuccess;
use Marvel\Http\Requests\OrderCreateRequest;
use Marvel\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;
    protected $orderService;
    protected $cartInventoryService;

    public function __construct(
        OrderService $orderService,
        CartInventoryService $cartInventoryService,
        private PaymentGatewayFactory $paymentGatewayFactory,
        private PaymentCheckoutHandler $paymentCheckoutHandler,
        private CashierQrService $cashierQrService,
    ) {
        $this->orderService = $orderService;
        $this->cartInventoryService = $cartInventoryService;
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->paginateForUser($request);

        return $this->apiResponse(
            FETCH_DATA_SUCCESSFULLY,
            200,
            true,
            new OrderCollection($orders)
        );
    }

    public function eligiblePromotions(): JsonResponse
    {
        $payload = $this->orderService->eligiblePromotionsForUser();

        if (!$payload) {
            return $this->apiResponse(CART_NOT_FOUND, 400, false);
        }

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $payload);
    }

    public function checkout(OrderCreateRequest $request)
    {
        $orderDataUser = $request->validated();
        $orderDataUser['user_id'] = $request->user()->id;
        
        $cart = $this->cartInventoryService->getActiveCartForUser($request->user());
        if (!$cart) {
            return $this->apiResponse(CART_NOT_FOUND, 400, false);
        }
        
        try {
            $this->cartInventoryService->ensureCartReservation($cart);
        } catch (\Throwable $e) {
            return $this->apiResponse($e->getMessage(), 400, false);
        }
        
        try {
            $orderPrice = $this->orderService->calcInvoicePrice($request);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        }
        
        if (!$orderPrice || $orderPrice <= 0) {
            return $this->apiResponse(FILED_TO_CREATE_ORDER_TRY_AGAIN, 500, false);
        }

        $paymentMethod = $request->input('payment_method', 'online');
        $gateway = $request->input('gateway', config('payment.default_gateway', 'myfatoorah'));
        $fulfillmentType = $request->input('fulfillment_type', 'delivery');

        if ($paymentMethod === 'cod' && $fulfillmentType === 'pickup') {
            return $this->apiResponse('COD is not available for pickup. Use pay_at_cashier instead.', 422, false);
        }

        $request->merge([
            'fulfillment_type' => $fulfillmentType,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $paymentMethod === 'online' ? $gateway : null,
        ]);

        try {
            $order = $this->orderService->addItemsInOrder($request);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        }

        if (!$order) {
            return $this->apiResponse(ERROR_ADDING_ITEMS_TO_ORDER, 500, false);
        }

        if ($paymentMethod === 'online') {
            return $this->paymentCheckoutHandler->handleOnlinePayment($request, $order, $orderPrice, $gateway);
        }

        if ($paymentMethod === 'cod') {
            return $this->paymentCheckoutHandler->handleCodPayment($request, $order);
        }

        if ($paymentMethod === 'pay_at_cashier') {
            return $this->paymentCheckoutHandler->handleCashierQrPayment($request, $order);
        }

        return $this->apiResponse('Invalid payment method', 422, false);
    }

    public function markCodAsPaid(int $orderId, Request $request): JsonResponse
    {
        $order = Order::query()->findOrFail($orderId);

        try {
            $this->orderService->markCodAsPaid($order);
        } catch (\RuntimeException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        }

        return $this->apiResponse(PAYMENT_SUCCESSFUL, 200, true);
    }

    public function markCashierPaid(int $orderId, Request $request): JsonResponse
    {
        $order = Order::query()->findOrFail($orderId);

        try {
            $this->orderService->markCashierPaid($order);
        } catch (\RuntimeException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        }

        return $this->apiResponse(PAYMENT_SUCCESSFUL, 200, true);
    }

    public function getTransactionQr(string $uuid, Request $request): \Illuminate\Http\Response|JsonResponse
    {
        $transaction = Transaction::byUuid($uuid)->first();

        if (!$transaction) {
            return $this->apiResponse('Transaction not found', 404, false);
        }

        $order = $transaction->order;
        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->apiResponse('Unauthorized', 403, false);
        }

        $svg = $this->cashierQrService->generateSvg($transaction);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }


    public function checkoutCallback(Request $request)
    {
        $paymentId = $request->query('paymentId', $request->input('paymentId'));
        if (!$paymentId) {
            return $this->apiResponse(MISSING_PAYMENT_ID, 400, false);
        }

        try {
            $gateway = $this->paymentGatewayFactory->make('myfatoorah');
        } catch (\App\Exceptions\UnsupportedGatewayException $e) {
            return $this->apiResponse('Payment gateway unavailable', 500, false);
        }

        $result = $gateway->verifyPayment($paymentId);

        $verifiedInvoiceId = $result->gatewayTransactionId;

        $transaction = Transaction::where('gateway_transaction_id', $verifiedInvoiceId)
            ->orWhere('invoice_id', $verifiedInvoiceId)
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => $result->status ?? ($result->success ? 'paid' : 'failed'),
                'gateway_response' => $result->rawResponse,
                'error_message' => $result->errorMessage,
                'paid_at' => $result->success ? now() : null,
            ]);
        }

        if (!$result->success) {
            $order = null;
            if ($transaction) {
                $order = $transaction->order;
                $this->orderService->changeOrderStatus($transaction->invoice_id, 'cancelled');
                try {
                    if ($order) {
                        event(new OrderCancelled($order));
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
                if ($order && ($user = User::find($order->user_id))) {
                    $cart = $this->cartInventoryService->getActiveCartForUser($user);
                    if ($cart) {
                        $this->cartInventoryService->releaseCart($cart, false);
                    }
                }
            }

            try {
                if ($order) {
                    event(new PaymentFailed($order));
                }
            } catch (\Throwable $e) {
                report($e);
            }

            $errorMessage = $result->errorMessage ?? __(PAYMENT_FAILED);

            return redirect(config('app.app_url_frontend') . '/' . app()->getLocale() . '/payment/failed?' . http_build_query([
                'status' => 'failed',
                'message' => $errorMessage,
                'payment_id' => $paymentId,
            ]));
        }

        $order = null;
        if ($transaction) {
            $order = $this->orderService->changeOrderStatus($transaction->invoice_id, 'completed');
            if ($order) {
                if ($user = User::find($order->user_id)) {
                    $cart = $this->cartInventoryService->getActiveCartForUser($user);
                    if ($cart) {
                        $shippingMethod = $order->shipping_method ?? ShippingMethod::SCHEDULED;
                        $this->cartInventoryService->finalizeItemsByShippingMethod($cart, $shippingMethod);

                        if ($order->coupon && $cart->fresh()->coupon === $order->coupon) {
                            $cart->fresh()->update(['coupon' => null]);
                        }
                    }
                }
            }
        }

        try {
            if ($order) {
                event(new PaymentSuccess($order));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if (request()->type === 'mobile') {
            return $this->apiResponse(CHECKOUT_SUCCESSFUL, 200, true, [
                'status' => 'success',
                'message' => __(PAYMENT_SUCCESSFUL),
                'payment_id' => $paymentId,
                'order_id' => $order?->id,
            ]);
        }

        return redirect(config('app.app_url_frontend') . '/' . app()->getLocale() . '/payment/success?' . http_build_query([
            'status' => 'success',
            'message' => __(PAYMENT_SUCCESSFUL),
            'payment_id' => $paymentId,
            'order_id' => $order?->id,
        ]));
        
    }

    public function checkoutErrorCallback(Request $request)
    {
        $paymentId = $request->query('paymentId', $request->input('paymentId'));
        if (!$paymentId) {
            return $this->apiResponse(MISSING_PAYMENT_ID, 400, false);
        }

        try {
            $gateway = $this->paymentGatewayFactory->make('myfatoorah');
        } catch (\App\Exceptions\UnsupportedGatewayException $e) {
            return $this->apiResponse('Payment gateway unavailable', 500, false);
        }

        $result = $gateway->verifyPayment($paymentId);
        $invoiceStatus = $result->status;
        $errorMessage = $result->errorMessage ?? __(PAYMENT_FAILED);

        $verifiedInvoiceId = $result->gatewayTransactionId;

        $transaction = Transaction::where('gateway_transaction_id', $verifiedInvoiceId)
            ->orWhere('invoice_id', $verifiedInvoiceId)
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => $result->rawResponse,
                'error_message' => $errorMessage,
            ]);
        }

        if ($transaction && (!$invoiceStatus || $invoiceStatus !== 'paid')) {
            $order = $this->orderService->changeOrderStatus($transaction->invoice_id, 'cancelled');
            try {
                if (isset($order) && $order) {
                    event(new OrderCancelled($order));
                }
            } catch (\Throwable $e) {
                report($e);
            }
            if ($order && ($user = User::find($order->user_id))) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->releaseCart($cart, false);
                }
            }

            try {
                if (isset($order) && $order) {
                    event(new PaymentFailed($order));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($request->type === 'mobile') {
            return $this->apiResponse(PAYMENT_FAILED, 400, false, [
                'status' => 'failed',
                'error' => $errorMessage,
                'payment_id' => $paymentId,
            ]);
        }

        return redirect(config('app.app_url_frontend') . '/' . app()->getLocale() . '/payment/failed?' . http_build_query([
            'status' => 'failed',
            'error' => $errorMessage,
            'payment_id' => $paymentId,
        ]));
    }
}
