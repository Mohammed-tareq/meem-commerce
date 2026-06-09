<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\FastShippingService;
use App\Services\General\MyfatoraService;
use App\Services\General\CartInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Marvel\Http\Requests\FastCheckoutRequest;
use Marvel\Traits\ApiResponse;

class FastShippingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private FastShippingService $fastShippingService,
        private MyfatoraService $myfatoraService,
        private CartInventoryService $cartInventoryService,
    ) {}

    public function status(): JsonResponse
    {
        $payload = $this->fastShippingService->getStatus();

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $payload);
    }

    public function products(Request $request): JsonResponse
    {
        $products = $this->fastShippingService->getFastShippingProducts($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $products);
    }

    public function checkout(FastCheckoutRequest $request): JsonResponse
    {
        $requestData = $request->validated();
        $requestData['user_id'] = $request->user()->id;

        $cart = $this->cartInventoryService->getActiveCartForUser($request->user());
        if (!$cart) {
            return $this->apiResponse('Cart not found', 400, false);
        }

        try {
            $this->cartInventoryService->ensureCartReservation($cart);
        } catch (\Throwable $e) {
            return $this->apiResponse($e->getMessage(), 400, false);
        }

        try {
            $order = $this->fastShippingService->createFastOrder($request);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponse($e->getMessage(), 422, false);
        } catch (\Throwable $e) {
            report($e);
            return $this->apiResponse(FILED_TO_CREATE_ORDER_TRY_AGAIN, 500, false);
        }

        $data = [
            'InvoiceValue' => $order->total_price,
            'CustomerName' => $requestData['name'],
            'NotificationOption' => 'LNK',
            'DisplayCurrencyIso' => 'EGP',
            'MobileCountryCode' => '+20',
            'CustomerMobile' => $requestData['user_phone'],
            'CustomerEmail' => $requestData['user_email'],
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

        $transaction = \Marvel\Database\Models\Transaction::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'invoice_id' => $invoiceId,
            'payment_method' => 'myfatoorah',
        ]);

        if (!$transaction) {
            return $this->apiResponse('Error creating transaction', 500, false);
        }

        return $this->apiResponse('Checkout successful', 200, true, ['url' => $invoiceUrl]);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = $this->fastShippingService->paginateFastOrders($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $orders);
    }
}
