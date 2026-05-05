<?php


namespace Marvel\Database\Repositories;

use Exception;
use Illuminate\Http\Request;
use Marvel\Database\Models\Coupon;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Marvel\Database\Models\Settings;
use Marvel\Database\Models\Shop;
use Marvel\Enums\CouponType;
use Marvel\Enums\Permission;
use Marvel\Exceptions\MarvelBadRequestException;

class CouponRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code'        => 'like',
        'name'        => 'like',

    ];

    protected $dataArray = [
        "name",
        'discount',
        'discount_type',
        'start_date',
        'end_date',
        'limiter',
        'status',
        "max_discount_amount",
    ];

    public function getDataArray(): array
    {
        return $this->dataArray;
    }

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Coupon::class;
    }
    public function modelQuery()
    {
        return Coupon::query();
    }

    /**
     * storeCoupon
     *
     * @param  mixed $request
     * @return mixed
     */
    public function storeCoupon(Request $request)
    {
        try {
            $data = $request->only($this->dataArray);
            return $this->create($data);
        } catch (Exception $th) {
            throw new MarvelBadRequestException(COULD_NOT_CREATE_THE_RESOURCE);
        }
    }
    public function updateCoupon($id, Request $request)
    {
        try {
            $coupon = $this->find($id);
            if (!$coupon) {
                throw new MarvelBadRequestException(COULD_NOT_UPDATE_THE_RESOURCE);
            }
            $data = $request->only($this->dataArray);

            $coupon->update($data);
            return $coupon;
        } catch (Exception $th) {
            throw new MarvelBadRequestException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }

    public function addCouponToCart($code)
    {
        $coupon = $this->where('code', $code)->first();
        if (!$coupon || !$coupon->isValid()) {
            throw new MarvelBadRequestException(COULD_NOT_ADD_COUPON_TO_CART_NOT_VALID);
        }

        $cart = auth()->user()->cart->first();
        if (!$cart || !$cart->items()->exists()) {
            throw new MarvelBadRequestException(COULD_NOT_ADD_COUPON_TO_EMPTY_CART);
        }

        if (!empty($cart->coupon)) {
            $existingCoupon = $this->where('code', $cart->coupon)->first();
            if ($existingCoupon && $existingCoupon->isValid()) {
                throw new MarvelBadRequestException(COULD_NOT_ADD_COUPON_TO_CART_YOU_HAVE_ALREADY_APPLIED_A_COUPON);
            }

            return  $cart->update(['coupon' => $coupon->code]);
        }
        return $cart->update(['coupon' => $coupon->code]);
    }
}
