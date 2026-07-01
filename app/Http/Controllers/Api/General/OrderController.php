<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Services\General\CartInventoryService;
use App\Services\General\MyfatoraService;
use App\Services\General\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Marvel\Database\Models\User;
use Marvel\Http\Requests\OrderCreateRequest;
use Marvel\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;
    protected $orderService;
    protected $myfatoraService;
    protected $cartInventoryService;

    public function __construct(OrderService $orderService, MyfatoraService $myfatoraService, CartInventoryService $cartInventoryService)
    {
        $this->orderService = $orderService;
        $this->myfatoraService = $myfatoraService;
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
        
        $data = [
            'InvoiceValue' => $orderPrice,
            'CustomerName' => "{$orderDataUser['name']}",
            'NotificationOption' => 'LNK',
            'DisplayCurrencyIso' => 'EGP',
            'MobileCountryCode' => '+20',
            'CustomerMobile' => $orderDataUser['user_phone'],
            'CustomerEmail' => $orderDataUser['user_email'],
            'language' => app()->getLocale() == 'ar' ? 'ar' : 'en',
            'CallBackUrl' => route('api.checkout.callback'),
            'ErrorUrl' => route('api.checkout.errorCallback'),
        ];
        
        $invoice = $this->myfatoraService->createInvoice($data);
        
        if (!is_array($invoice)) {
            return $this->apiResponse(ERROR_CREATING_INVOICE, 500, false);
        }
        
        $invoiceUrl = data_get($invoice, 'Data.InvoiceURL');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');
        
        if (!$invoiceUrl || !$invoiceId) {
            return $this->apiResponse(ERROR_CREATING_INVOICE, 500, false);
        }
        
        
        try {
            $order = $this->orderService->addItemsInOrder($request);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        }
        
        if (!$order) {
            return $this->apiResponse(ERROR_ADDING_ITEMS_TO_ORDER, 500, false);
        }
        
        if (!$this->orderService->createTransaction($order->id, $invoiceId, 'myfatoorah')) {
            return $this->apiResponse(ERROR_CREATING_TRANSACTION, 500, false);
        }
        

        return $this->apiResponse(CHECKOUT_SUCCESSFUL, 200, true, ['url' => $invoiceUrl]);
    }


    public function checkoutCallback(Request $request)
    {
        $paymentId = $request->query('paymentId', $request->input('paymentId'));
        if (!$paymentId) {
            return $this->apiResponse(MISSING_PAYMENT_ID, 400, false);
        }


        $data = [
            'Key' => $paymentId,
            'KeyType' => 'PaymentId',
        ];

        $invoice = $this->myfatoraService->checkInvoice($data);

        if (!is_array($invoice)) {
            return $this->apiResponse(INVALID_PAYMENT_RESPONSE, 400, false);
        }

        $invoiceStatus = data_get($invoice, 'Data.InvoiceStatus');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        if (!$invoiceStatus || !$invoiceId) {
            return $this->apiResponse(INVALID_PAYMENT_RESPONSE, 400, false);
        }

        if ($invoiceStatus !== 'Paid') {
            $order = $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
            if ($order && ($user = User::find($order->user_id))) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->releaseCart($cart, false);
                }
            }

            $errorMessage = data_get($invoice, 'Data.InvoiceError')
                ?? data_get($invoice, 'Data.InvoiceTransactions.0.Error')
                ?? __(PAYMENT_FAILED);

            return redirect(config('app.app_url_frontend') . '/' . app()->getLocale() . '/payment/failed?' . http_build_query([
                'status' => 'failed',
                'message' => $errorMessage,
                'payment_id' => $paymentId,
            ]));
        }

        $order = $this->orderService->changeOrderStatus($invoiceId, 'completed');
        if ($order) {
            if ($user = User::find($order->user_id)) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->finalizeCart($cart);
                }
            }
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

        $data = [
            'Key' => $paymentId,
            'KeyType' => 'PaymentId',
        ];

        $invoice = $this->myfatoraService->checkInvoice($data);
        $invoiceStatus = data_get($invoice, 'Data.InvoiceStatus');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        $errorMessage = data_get($invoice, 'Data.InvoiceError')
            ?? data_get($invoice, 'Data.InvoiceTransactions.0.Error')
            ?? __(PAYMENT_FAILED);

        if ($invoiceStatus && $invoiceStatus !== 'Paid' && $invoiceId) {
            $order = $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
            if ($order && ($user = User::find($order->user_id))) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->releaseCart($cart, false);
                }
            }
        }

        return redirect(config('app.app_url_frontend') . '/' . app()->getLocale() . '/payment/failed?' . http_build_query([
            'status' => 'failed',
            'error' => $errorMessage,
            'payment_id' => $paymentId,
        ]));
    }
}
