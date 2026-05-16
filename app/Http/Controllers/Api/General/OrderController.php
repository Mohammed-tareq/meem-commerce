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

    public function checkout(OrderCreateRequest $request)
    {
        $orderDataUser = $request->validated();
        $orderDataUser['user_id'] = $request->user()->id;

        $cart = $this->cartInventoryService->getActiveCartForUser($request->user());
        if (!$cart) {
            return $this->apiResponse('Cart not found', 400, false);
        }

        try {
            $this->cartInventoryService->ensureCartReservation($cart);
        } catch (\Throwable $e) {
            return $this->apiResponse($e->getMessage(), 400, false);
        }

        $orderPrice = $this->orderService->calcInvoicePrice($request);
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
            return $this->apiResponse('Error creating invoice', 500, false);
        }

        $invoiceUrl = data_get($invoice, 'Data.InvoiceURL');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        if (!$invoiceUrl || !$invoiceId) {
            return $this->apiResponse('Error creating invoice', 500, false);
        }


        if (!$order = $this->orderService->addItemsInOrder($request)) {
            return $this->apiResponse('Error adding items to order', 500, false);
        }

        if (!$this->orderService->createTransaction($order->id, $invoiceId, 'myfatoorah')) {
            return $this->apiResponse('Error creating transaction', 500, false);
        }

        return $this->apiResponse('Checkout successful', 200, true, ['url' => $invoiceUrl]);
    }


    public function checkoutCallback(Request $request)
    {
        $paymentId = $request->query('paymentId', $request->input('paymentId'));
        if (!$paymentId) {
            return $this->apiResponse('Missing payment id', 400, false);
        }


        $data = [
            'Key' => $paymentId,
            'KeyType' => 'PaymentId',
        ];

        $invoice = $this->myfatoraService->checkInvoice($data);

        if (!is_array($invoice)) {
            return $this->apiResponse('Invalid payment response', 400, false);
        }

        $invoiceStatus = data_get($invoice, 'Data.InvoiceStatus');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        if (!$invoiceStatus || !$invoiceId) {
            return $this->apiResponse('Invalid payment response', 400, false);
        }

        if ($invoiceStatus !== 'Paid') {
            $order = $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
            if ($order && ($user = User::find($order->user_id))) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->releaseCart($cart, false);
                }
            }
            return $this->apiResponse('Payment failed', 400, false);
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
        // if ($order = $this->orderService->getOrder($invoice['Data']["InvoiceId"])) {
        //     $this->orderService->sendAdminNotification($order);
        // }
        return $this->apiResponse('Payment successful', 200, true);
    }

    public function checkoutErrorCallback(Request $request)
    {
        $paymentId = $request->query('paymentId', $request->input('paymentId'));
        if (!$paymentId) {
            return $this->apiResponse('Missing payment id', 400, false);
        }

        $data = [
            'Key' => $paymentId,
            'KeyType' => 'PaymentId',
        ];

        $invoice = $this->myfatoraService->checkInvoice($data);
        $invoiceStatus = data_get($invoice, 'Data.InvoiceStatus');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        if ($invoiceStatus && $invoiceStatus !== 'Paid' && $invoiceId) {
            $order = $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
            if ($order && ($user = User::find($order->user_id))) {
                $cart = $this->cartInventoryService->getActiveCartForUser($user);
                if ($cart) {
                    $this->cartInventoryService->releaseCart($cart, false);
                }
            }
        }
        return $this->apiResponse('Payment failed', 400, false);
    }
}
