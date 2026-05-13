<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\OrderService;
use Marvel\Http\Requests\OrderCreateRequest;
use Marvel\Traits\ApiResponse;

use const Dom\NO_DATA_ALLOWED_ERR;

class OrderController extends Controller
{
    use ApiResponse;
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(OrderCreateRequest $request)
    {
        if (!$orders = $this->orderService->calcInvoicePrice($request)) {
            return $this->apiResponse(FILED_TO_CREATE_ORDER_TRY_AGAIN, 500,false);
        }
        return $this->apiResponse('Order created successfully', 201 ,true);
    }
}
