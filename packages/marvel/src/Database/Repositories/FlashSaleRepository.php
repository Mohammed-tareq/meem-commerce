<?php


namespace Marvel\Database\Repositories;

use Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Traits\MediaManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FlashSaleRepository extends BaseRepository
{
    use MediaManager;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title' => 'like',
        //        'language',
    ];

    /**
     * @var array
     */
    protected $dataArray = [
        'title',
        'description',
        'start_date',
        'end_date',
        'type',
        'status',
        'max_discount_amount',
        'discount',
    ];


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
        return FlashSale::class;
    }

    public function modelQuery()
    {
        return FlashSale::query();
    }


    /**
     * storeFlashSale
     *
     * @param  mixed $request
     * @return void
     */
    public function storeFlashSale($request): FlashSale
    {
        try {
            // only admin can create flash deals
            DB::beginTransaction();
            $flash_sale = $this->create($request->except('image'));

            if ($request->hasFile('image')) {
                if (!$this->uploadSingleImage($request, 'image', $flash_sale, 'flash-sales-image', 'flashSales')) {
                    throw new HttpException(422, 'Flash sale image upload failed, please check the file format or size.');
                }
            }

            DB::commit();
            return $flash_sale;
        } catch (Exception $th) {
            DB::rollBack();
            throw new Exception(SOMETHING_WENT_WRONG, $th->getMessage());
        }
    }


    /**
     * updateFlashSale
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateFlashSale(Request $request, $id)
    {
        try {
            // only admin can update flash deals
            DB::beginTransaction();
            $flash_sale = $this->findOrFail($id);
            $flash_sale->update($request->except('image'));

            if ($request->hasFile('image')) {
                if (!$this->updateSingleImage($request, 'image', $flash_sale, 'flash-sales-image', 'flashSales')) {
                    throw new HttpException(422, 'Flash sale image upload failed, please check the file format or size.');
                }
            }

            DB::commit();
            $this->updateFlashSaleProductPrices($flash_sale);
            return $flash_sale;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(SOMETHING_WENT_WRONG, $e->getMessage());
        }
    }

    /**
     * setProductInFlashSale
     *
     * @param  array $product_ids
     * @return void
     */
    public function setProductInFlashSale($product_ids)
    {
        foreach ($product_ids as $key => $product_id) {
            $product = Product::findOrFail($product_id);
            $product->in_flash_sale = true;
            $product->save();
        }
    }


    /**
     * unsetProductFromFlashSale
     *
     * @param  array $previous_list
     * @param  array $new_list
     * @return void
     */
    public function unsetProductFromFlashSale($previous_list, $new_list)
    {
        $final_list = array_diff($previous_list, $new_list);

        if (isset($final_list)) {
            foreach ($final_list as $key => $product_id) {
                $product = Product::findOrFail($product_id);
                $product->in_flash_sale = false;
                $product->save();
            }
        }
    }

    private function updateFlashSaleProductPrices(FlashSale $flashSale)
    {
        $flashSale->load('products');
        $now = now();
        $isActive = $flashSale->sale_status
            && $flashSale->start_date
            && $flashSale->end_date
            && $now->between($flashSale->start_date, $flashSale->end_date);

        foreach ($flashSale->products as $product) {
            if (!$isActive) {
                $product->price_after_flash_sale = null;
                $product->save();
                continue;
            }

            $basePrice = $product->getDiscountedPrice() ?? $product->price;
            $product->price_after_flash_sale = $flashSale->calcPrice($basePrice);
            $product->save();
        }
    }
}
