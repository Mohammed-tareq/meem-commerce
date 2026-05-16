<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\MyfatoraService;
use App\Services\General\OrderService;
use Illuminate\Http\Request;
use Marvel\Http\Requests\OrderCreateRequest;
use Marvel\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;
    protected $orderService;
    protected $myfatoraService;

    public function __construct(OrderService $orderService, MyfatoraService $myfatoraService)
    {
        $this->orderService = $orderService;
        $this->myfatoraService = $myfatoraService;
    }

    public function checkout(OrderCreateRequest $request)
    {
        $orderDataUser = $request->validated();
        $orderDataUser['user_id'] = $request->user()->id;
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
            'CallBackUrl' => 'http://localhost:8000/api/v1/general/checkout/callback',
            'ErrorUrl' => 'http://localhost:8000/api/v1/general/checkout/errorCallback',
        ];

        $invoice = $this->myfatoraService->createInvoice($data);
        $invoiceUrl = data_get($invoice, 'Data.InvoiceURL');
        $invoiceId = data_get($invoice, 'Data.InvoiceId');

        if (!$invoice || !$invoiceUrl || !$invoiceId) {
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
        $invoiceStatus = $invoice['Data']['InvoiceStatus'] ?? null;
        $invoiceId = $invoice['Data']['InvoiceId'] ?? null;

        if (!$invoice || !$invoiceStatus || !$invoiceId) {
            return $this->apiResponse('Invalid payment response', 400, false);
        }

        if ($invoiceStatus !== 'Paid') {
            $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
            return $this->apiResponse('Payment failed', 400, false);
        }

        $order = $this->orderService->changeOrderStatus($invoiceId, 'completed');
        if ($order) {
            $this->orderService->clearCart($order->user_id);
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
            $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
        }
        return $this->apiResponse('Payment failed', 400, false);
    }
}
