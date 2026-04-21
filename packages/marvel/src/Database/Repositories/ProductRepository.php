<?php


namespace Marvel\Database\Repositories;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Marvel\Database\Models\Availability;
use Marvel\Database\Models\DigitalFile;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Resource;
use Marvel\Database\Models\Type;
use Marvel\Database\Models\Variation;
use Marvel\Enums\DiscountType;
use Marvel\Enums\ProductStatus;
use Marvel\Enums\ProductType;
use Marvel\Traits\MediaManager;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use Marvel\Enums\Permission;
use Marvel\Events\ProductReviewApproved;
use Marvel\Events\ProductReviewRejected;
use Marvel\Events\DigitalProductUpdateEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Marvel\Exceptions\MarvelException;

class ProductRepository extends BaseRepository
{

    use MediaManager;

    /**
     * @var array
     */
    // protected $fieldSearchable = [
    //     'name' => 'like',
    //     'shop_id',
    //     // 'status',
    //     // 'is_rental',
    //     // 'type.slug' => 'in',
    //     // 'dropoff_locations.slug' => 'in',
    //     // 'pickup_locations.slug' => 'in',
    //     // 'persons.slug' => 'in',
    //     // 'deposits.slug' => 'in',
    //     // 'features.slug' => 'in',
    //     // 'categories.slug' => 'in',
    //     // 'tags.slug' => 'in',
    //     // 'author.slug',
    //     // 'manufacturer.slug' => 'in',
    //     // 'min_price' => 'between',
    //     // 'max_price' => '>=',
    //     'price' => 'between',
    //     // 'language',
    //     // 'metas.key',
    //     // 'metas.value',
    //     // 'product_type',
    //     // 'visibility'
    // ];

    // protected $dataArray = [
    //     'name',
    //     'slug',
    //     'price',
    //     'sale_price',
    //     'product_type',
    //     'quantity',
    //     'description',
    //     'sku',
    //     'status',
    //     'height',
    //     'length',
    //     'width',
    //     'in_stock',
    //     'has_discount',
    //     'has_flash_sale',
    // 'max_price',
    // 'min_price',
    // 'type_id',
    // 'author_id',
    // 'language',
    // 'manufacturer_id',
    // 'unit',
    // 'is_digital',
    // 'is_external',
    // 'external_product_url',
    // 'external_product_button_text',
    // 'image',
    // 'gallery',
    // 'video',
    // ];
    // public function getProductDataArray(): array
    // {
    //     return $this->dataArray;
    // }

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
        return Product::class;
    }


    /**
     * processFlashSaleProducts
     *
     * @param  Request $request
     * @return object
     */
    public function processFlashSaleProducts(Request $request, $products_query)
    {
        $user = $request->user();
        switch ($user) {
            case $user->hasPermissionTo(Permission::SUPER_ADMIN):

                // if condition : during deal data build
                // else condition : when he entered into vendor shop & check
                if ($request->searchedByUser === 'super_admin_builder') {

                    $shop_id = $request->shop_id ?? null;
                    $author_id = $request->author ?? null;
                    $manufacturer_id = $request->manufacturer ?? null;

                    $products_query = $products_query->where('in_flash_sale', '=', false)
                        ->where('sale_price', '=', null)
                        ->whereNotIn('id', function ($query) {
                            $query->select('product_id')->from('flash_sale_requests_products');
                        })
                        ->when($shop_id, function ($products_query) use ($shop_id) {
                            return $products_query->where('shop_id', '=', $shop_id);
                        })
                        ->when($author_id, function ($products_query) use ($author_id) {
                            return $products_query->where('author_id', '=', $author_id);
                        })
                        ->when($manufacturer_id, function ($products_query) use ($manufacturer_id) {
                            return $products_query->where('manufacturer_id', '=', $manufacturer_id);
                        });
                } else {
                    $products_query = $products_query->where('in_flash_sale', '=', true)->where('shop_id', '=', $request->shop_id);
                }

                break;

            case $user->hasPermissionTo(Permission::STORE_OWNER):

                // if condition : when he want to see shop specific products
                // else condition : fetched all deal products of vendor's listed all shops. This can be used in vendor root page route
                if ($request->shop_id) {
                    // if : fetching shop product for building flash sale request
                    // else : just seeing which products are selected for flash sale of this shop
                    if ($request->searchedByUser === 'vendor') {
                        $products_query = $products_query->where('in_flash_sale', '=', false)
                            ->where('shop_id', '=', $request->shop_id)
                            ->where('sale_price', '=', null);
                    } else {
                        $products_query = $products_query->where('in_flash_sale', '=', true);
                    }
                } else {
                    $products_query = $products_query->where('in_flash_sale', '=', true)->whereIn('shop_id', $user->shops->pluck('id'));
                }

                break;

            case $user->hasPermissionTo(Permission::STAFF):

                // staff can see only his assigned shop's deals product
                $products_query = $products_query->where('in_flash_sale', '=', true);
                break;


            case $user->hasPermissionTo(Permission::CUSTOMER):

                // customer can see all the products of a deal
                $products_query = $products_query->where('in_flash_sale', '=', true);
                break;
        }

        return $products_query;
    }


    /**
     * storeProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function storeProduct(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->except(['images', 'categories']);
            $data['slug'] = $this->makeSlug($request);
            $price = $data['price'] ?? null;
            $hasDiscount = !empty($data['has_discount']);
            $discountType = $data['discount_type'] ?? DiscountType::PERCENTAGE;
            $amount = $data['amount'] ?? 0;

            $data['price_after_discount'] = $hasDiscount
                ? $this->calculateDiscountedPrice($price, $discountType, $amount)
                : null;

            $hasFlashSale = !empty($data['has_flash_sale']);
            $flashSaleId = $data['flash_sale_id'] ?? null;
            $flashSale = $this->resolveFlashSale($flashSaleId, null, $hasFlashSale);
            $basePriceForFlashSale = $hasDiscount && $data['price_after_discount'] !== null
                ? $data['price_after_discount']
                : $price;
            $data['price_after_flash_sale'] = $this->calculateFlashSalePrice($flashSale, $basePriceForFlashSale);

            $product = $this->create($data);
            if ($request->has('images')) {
                if (!$this->uploadImages($request, 'images', $product, 'products', 'products')) {
                    throw new HttpException(422, 'Images Products upload failed, please check the file format or size.');
                }
            }


            if (isset($request['categories'])) {
                $product->categories()->attach($request['categories']);
            }

            if (!empty($data['has_flash_sale']) && $data['has_flash_sale'] === true) {
                $flashSaleId = $data['flash_sale_id'] ?? null;

                if ($flashSaleId) {
                    $product->flash_sales()->sync([$flashSaleId]);
                }
            }
            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function checkProductForPublish($request, $product)
    {
        $status = '';
        if ($product->shop['owner']['id'] == $request->user()->id) {
            if ($product->status == ProductStatus::DRAFT || $product->status == ProductStatus::UNDER_REVIEW || $product->status == ProductStatus::REJECTED) {
                if ($request->status == ProductStatus::DRAFT) {
                    $status = ProductStatus::DRAFT;
                } elseif ($request->status == ProductStatus::UNDER_REVIEW) {
                    $status = ProductStatus::UNDER_REVIEW;
                } else {
                    $status = ProductStatus::DRAFT;
                }
            } elseif ($product->status == ProductStatus::APPROVED || $product->status == ProductStatus::PUBLISH || $product->status == ProductStatus::UNPUBLISH) {
                if ($request->status == ProductStatus::PUBLISH) {
                    $status = ProductStatus::PUBLISH;
                } elseif ($request->status == ProductStatus::UNPUBLISH) {
                    $status = ProductStatus::UNPUBLISH;
                } else {
                    $status = ProductStatus::UNPUBLISH;
                }
            }
        } elseif ($request->user()->hasPermissionTo(Permission::SUPER_ADMIN)) {
            if ($request->status == ProductStatus::APPROVED) {
                $status = ProductStatus::PUBLISH;
                event(new ProductReviewApproved($product));
            } elseif ($request->status == ProductStatus::REJECTED) {
                $status = ProductStatus::REJECTED;
                event(new ProductReviewRejected($product));
            } elseif ($request->status == ProductStatus::PUBLISH) {
                return ProductStatus::PUBLISH;
            } elseif ($request->status == ProductStatus::UNPUBLISH) {
                $status = ProductStatus::UNPUBLISH;
            } else {
                $status = ProductStatus::REJECTED;
            }
        } else {
            $status = ProductStatus::REJECTED;
        }
        return $status;
    }

    /**
     * updateProduct
     *
     * @param  $request
     * @param  $id
     * @param  $setting
     * @return void
     */
    // public function updateProduct($request, $id)
    // {
    //     try {
    //         $product = $this->findOrFail($id);

    //         if (is_array($request['metas'])) {
    //             foreach ($request['metas'] as $key => $value) {
    //                 $metas[$value['key']] = $value['value'];
    //                 $product->setMeta($metas);
    //             }
    //         }

    //         if (isset($request['categories'])) {
    //             $product->categories()->sync($request['categories']);
    //         }
    //         if (isset($request['tags'])) {
    //             $product->tags()->sync($request['tags']);
    //         }
    //         if (isset($request['dropoff_locations'])) {
    //             $product->dropoff_locations()->sync($request['dropoff_locations']);
    //         }
    //         if (isset($request['pickup_locations'])) {
    //             $product->pickup_locations()->sync($request['pickup_locations']);
    //         }
    //         if (isset($request['variations'])) {
    //             $product->variations()->sync($request['variations']);
    //         }
    //         if (isset($request['persons'])) {
    //             $product->persons()->sync($request['persons']);
    //         }
    //         if (isset($request['features'])) {
    //             $product->features()->sync($request['features']);
    //         }
    //         if (isset($request['deposits'])) {
    //             $product->deposits()->sync($request['deposits']);
    //         }
    //         if (isset($request['digital_file'])) {
    //             $file = $request['digital_file'];
    //             if (isset($file['id'])) {
    //                 $product->digital_file()->where('id', $file['id'])->update($file);
    //             } else {
    //                 $product->digital_file()->create($file);
    //             }
    //         }
    //         if (isset($request['variation_options'])) {
    //             if (isset($request['variation_options']['upsert'])) {
    //                 foreach ($request['variation_options']['upsert'] as $key => $variation) {

    //                     $variation['sale_price'] = isset($variation['sale_price']) ? $variation['sale_price'] : null;

    //                     if (isset($variation['is_digital']) && $variation['is_digital']) {

    //                         $file = $variation['digital_file'];
    //                         unset($variation['digital_file']);
    //                         unset($variation['inform_purchased_customer']);
    //                         unset($variation['product_update_message']);

    //                         if (isset($variation['id'])) {
    //                             $product->variation_options()->where('id', $variation['id'])->update($variation);

    //                             try {
    //                                 $updated_variation = Variation::findOrFail($variation['id']);
    //                             } catch (Exception $e) {
    //                                 throw new ModelNotFoundException(NOT_FOUND);
    //                             }

    //                             if (TRANSLATION_ENABLED) {
    //                                 Variation::where('sku', $updated_variation->sku)->where('id', '=', $updated_variation->id)->update([
    //                                     'price' => $updated_variation->price,
    //                                     'sale_price' => $updated_variation->sale_price,
    //                                     'quantity' => $updated_variation->quantity,
    //                                 ]);
    //                             }


    //                             if (isset($updated_variation->digital_file_tracker)) {
    //                                 if (isset($file['attachment_id'])) {
    //                                     $updated_variation->digital_file()->where('fileable_id', $updated_variation->id)->update($file);
    //                                     $updated_digital_file = DigitalFile::where('fileable_id', $updated_variation->id)->first();
    //                                     $updated_variation->update([
    //                                         'digital_file_tracker' => $updated_digital_file->id,
    //                                     ]);
    //                                 }
    //                             } else {
    //                                 $created_digital_file = $updated_variation->digital_file()->create($file);
    //                                 $updated_variation->update([
    //                                     'digital_file_tracker' => $created_digital_file->id,
    //                                 ]);
    //                             }
    //                         } else {
    //                             $new_variation = $product->variation_options()->create($variation);
    //                             $digital_file = $new_variation->digital_file()->create($file);
    //                             $new_variation->update([
    //                                 'digital_file_tracker' => $digital_file->id
    //                             ]);
    //                         }
    //                     } else {
    //                         if (isset($variation['id'])) {
    //                             $product->variation_options()->where('id', $variation['id'])->update($variation);
    //                         } else {
    //                             $product->variation_options()->create($variation);
    //                         }
    //                     }
    //                 }
    //             }
    //             if (isset($request['variation_options']['delete'])) {
    //                 foreach ($request['variation_options']['delete'] as $key => $id) {
    //                     try {
    //                         $product->variation_options()->where('id', $id)->delete();
    //                     } catch (Exception $e) {
    //                         //
    //                     }
    //                 }
    //             }
    //         }
    //         $data = $request->only($this->dataArray);
    //         $data['sale_price'] = isset($request['sale_price']) ? $request['sale_price'] : null;

    //         if ($setting->options["isProductReview"]) {
    //             $data['status'] = $this->checkProductForPublish($request, $product);
    //         }

    //         if ($request->product_type == ProductType::VARIABLE) {
    //             $data['price'] = NULL;
    //             $data['sale_price'] = NULL;
    //             $data['sku'] = NULL;
    //         }
    //         if ($request->product_type == ProductType::SIMPLE) {
    //             $data['max_price'] = $data['price'];
    //             $data['min_price'] = $data['price'];
    //         }

    //         if (!empty($request->slug) && $request->slug != $product->slug) {
    //             $stringifySlug = $this->makeSlug($request);
    //             $data['slug'] = $this->makeSlug($request);

    //             if (TRANSLATION_ENABLED) {
    //                 $this->where('slug', $product->slug)->where('id', '!=', $product->id)->update([
    //                     'slug' => $stringifySlug
    //                 ]);
    //             }
    //         }

    //         $product->update($data);
    //         if ($product->product_type === ProductType::SIMPLE) {
    //             $product->variations()->delete();
    //             $product->variation_options()->delete();
    //         }
    //         $product->save();

    //         if (TRANSLATION_ENABLED) {
    //             $this->where('sku', $product->sku)->where('id', '=', $product->id)->update([
    //                 'price' => $product->price,
    //                 'sale_price' => $product->sale_price,
    //                 'max_price' => $product->max_price,
    //                 'min_price' => $product->min_price,
    //                 'unit' => $product->unit,
    //                 'quantity' => $product->quantity,
    //             ]);
    //         }

    //         if ($setting->options["enableEmailForDigitalProduct"]) {
    //             if ($request->product_type == 'variable') {
    //                 foreach ($request['variation_options']['upsert'] as $variation_data) {
    //                     if ($variation_data['inform_purchased_customer']) {
    //                         event(new DigitalProductUpdateEvent($product, $request->user(), [
    //                             'inform_customer' => $variation_data['inform_purchased_customer'],
    //                             'update_message' => $variation_data['product_update_message'] ?? ''
    //                         ]));
    //                     }
    //                 }
    //             } else {
    //                 if ($request->inform_purchased_customer) {
    //                     event(new DigitalProductUpdateEvent($product, $request->user(), [
    //                         'inform_customer' => $request->inform_purchased_customer,
    //                         'update_message' => $request->product_update_message
    //                     ]));
    //                 }
    //             }
    //         }

    //         return $product;
    //     } catch (Exception $e) {
    //         throw $e;
    //     }
    // }


    public function updateProduct(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $product = Product::find($id);

            $data = $request->except(['images', 'categories']);

            if ($request->has('slug')) {
                $data['slug'] = $this->makeSlug($request, $product->id);
            }

            $price = array_key_exists('price', $data) ? $data['price'] : $product->price;
            $hasDiscount = array_key_exists('has_discount', $data) ? (bool) $data['has_discount'] : $product->has_discount;
            $discountType = $data['discount_type'] ?? $product->discount_type ?? DiscountType::PERCENTAGE;
            $amount = array_key_exists('amount', $data) ? $data['amount'] : $product->amount;

            $data['price_after_discount'] = $hasDiscount
                ? $this->calculateDiscountedPrice($price, $discountType, $amount)
                : null;

            $hasFlashSale = array_key_exists('has_flash_sale', $data) ? (bool) $data['has_flash_sale'] : $product->has_flash_sale;
            $flashSaleId = $data['flash_sale_id'] ?? null;
            $flashSale = $this->resolveFlashSale($flashSaleId, $product, $hasFlashSale);
            $basePriceForFlashSale = $hasDiscount && $data['price_after_discount'] !== null
                ? $data['price_after_discount']
                : $price;
            $data['price_after_flash_sale'] = $this->calculateFlashSalePrice($flashSale, $basePriceForFlashSale);

            $product->update($data);

            if ($request->has('images')) {
                if (!$this->updateImages($request, 'images', $product, 'products', 'products')) {
                    throw new HttpException(422, 'Images Products upload failed, please check the file format or size.');
                }
            }

            if ($request->has('categories')) {
                $product->categories()->sync($request['categories']);
            }

            if (!empty($data['has_flash_sale']) && $data['has_flash_sale'] === true) {
                $flashSaleId = $data['flash_sale_id'] ?? null;

                if ($flashSaleId) {
                    $product->flash_sales()->sync([$flashSaleId]);
                }
            } else {
                $product->flash_sales()->detach();
            }
            DB::commit();

            return $product->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }


    /**
     * getBestSellingProducts
     *
     * @param $request
     * @return void
     */

    public function getBestSellingProducts($request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $language = $request->language ?? DEFAULT_LANGUAGE;
        $range = !empty($request->range) && $request->range !== 'undefined' ? $request->range : '';
        $type_id = $request->type_id ? $request->type_id : '';
        if (isset($request->type_slug) && empty($type_id)) {
            try {
                $type = Type::where('slug', $request->type_slug)->where('language', $language)->firstOrFail();
                $type_id = $type->id;
            } catch (ModelNotFoundException $e) {
                throw new MarvelException(NOT_FOUND);
            }
        }

        $products_query = Product::leftJoin('order_product', 'order_product.product_id', 'products.id')
            ->leftJoin('orders', 'order_product.order_id', '=', 'orders.id')
            ->with(['type', 'shop'])
            ->selectRaw('products.*, sum(order_product.order_quantity) total_sales')
            ->where('orders.parent_id', null)
            ->where('orders.order_status', 'order-completed')
            ->where('orders.language', $language)
            ->groupBy('order_product.product_id')
            ->orderBy('total_sales', 'desc');

        if (isset($request->shop_id)) {
            $products_query = $products_query->where('shop_id', "=", $request->shop_id);
        }
        if ($range) {
            $products_query = $products_query->whereDate('created_at', '>', Carbon::now()->subDays($range));
        }
        if ($type_id) {
            $products_query = $products_query->where('type_id', '=', $type_id);
        }
        return $products_query->take($limit)->get();
    }

    public function fetchRelated($id, $limit = 10)
    {
        try {
            $product = $this->findOrFail($id);
            $categories = $product->categories->pluck('id');

            return $this->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            })
                ->where('id', '!=', $id)
                ->limit($limit)->get() ?? collect();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUnavailableProducts($from, $to)
    {
        $_blockedDates = Availability::whereDate('from', '<=', $from)
            ->whereDate('to', '>=', $to)
            ->get()->groupBy('product_id');

        $unavailableProducts = [];

        foreach ($_blockedDates as $productId => $date) {
            if (!$this->isProductAvailableAt($from, $to, $productId, $date)) {
                $unavailableProducts[] = $productId;
            }
        }
        return $unavailableProducts;
    }

    public function isProductAvailableAt($from, $to, $productId, $_blockedDates, $requestedQuantity = 1)
    {
        $quantity = 0;
        try {
            $product = Product::findOrFail($productId);
        } catch (\Throwable $th) {
            throw $th;
        }

        foreach ($_blockedDates as $singleDate) {
            $period = Period::make($singleDate['from'], $singleDate['to'], Precision::DAY, Boundaries::EXCLUDE_END);
            $range = Period::make($from, $to, Precision::DAY, Boundaries::EXCLUDE_END);
            if ($period->overlapsWith($range)) {
                $quantity += $singleDate->order_quantity;
            }
        }
        return $product->quantity - $quantity > $requestedQuantity;
    }


    public function fetchBlockedDatesForAProductInRange($from, $to, $productId)
    {
        return Availability::where('product_id', $productId)->whereDate('from', '>=', $from)->whereDate('to', '<=', $to)->get();
    }

    public function fetchBlockedDatesForAVariationInRange($from, $to, $variation_id)
    {
        return Availability::where('bookable_id', $variation_id)->where('bookable_type', 'Marvel\Database\Models\Variation')->whereDate('from', '>=', $from)->whereDate('to', '<=', $to)->get();
    }

    public function isVariationAvailableAt($from, $to, $variationId, $_blockedDates, $requestedQuantity)
    {
        $quantity = 0;
        try {
            $variation = Variation::findOrFail($variationId);
        } catch (\Throwable $th) {
            throw $th;
        }

        foreach ($_blockedDates as $singleDate) {
            $period = Period::make($singleDate['from'], $singleDate['to'], Precision::DAY, Boundaries::EXCLUDE_END);
            $range = Period::make($from, $to, Precision::DAY, Boundaries::EXCLUDE_END);
            if ($period->overlapsWith($range)) {
                $quantity += $singleDate->order_quantity;
            }
        }
        return $variation->quantity - $quantity >= $requestedQuantity;
    }


    public function calculatePrice($bookedDay, $product_id, $variation_id, $quantity, $persons, $dropoff_location_id, $pickup_location_id, $deposits, $features)
    {
        $price = 0;
        $person_price = 0;
        $deposit_price = 0;
        $feature_price = 0;
        $dropoff_location_price = 0;
        $pickup_location_price = 0;

        if ($variation_id) {
            $variation_price = $this->calculateVariationPrice($variation_id);
            $price += $variation_price * $bookedDay * $quantity;
        } else {
            $product_price = $this->calculateProductPrice($product_id);
            $price += $product_price * $bookedDay * $quantity;
        }
        if ($dropoff_location_id) {
            $dropoff_location_price = $this->calculateLocationPrice($dropoff_location_id);
        }
        if ($pickup_location_id) {
            $pickup_location_price = $this->calculateLocationPrice($pickup_location_id);
        }
        if ($features) {
            $feature_price = $this->calculateResourcePrice($features);
        }
        if ($persons) {
            $person_price = $this->calculateResourcePrice($persons);
        }
        if ($deposits) {
            $deposit_price = $this->calculateResourcePrice($deposits);
        }

        return [
            'totalPrice' => $price + $person_price + $deposit_price + $feature_price + $dropoff_location_price,
            $pickup_location_price,
            'personPrice' => $person_price,
            'depositPrice' => $deposit_price,
            'featurePrice' => $feature_price,
            'dropoffLocationPrice' => $dropoff_location_price,
            'pickupLocationPrice' => $pickup_location_price
        ];
    }

    public function calculateProductPrice($product_id)
    {
        try {
            $product = Product::findOrFail($product_id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return $product->sale_price ? $product->sale_price : $product->price;
    }

    public function calculateVariationPrice($variation_id)
    {
        try {
            $variation = Variation::findOrFail($variation_id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return $variation->sale_price ? $variation->sale_price : $variation->price;
    }

    public function calculateLocationPrice($location_id)
    {
        try {
            $location = Resource::findOrFail($location_id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return $location->price;
    }

    public function calculateResourcePrice($resources)
    {
        $price = 0;
        foreach ($resources as $resource_id) {
            try {
                $resource = Resource::findOrFail($resource_id);
            } catch (\Throwable $th) {
                throw $th;
            }
            if ($resource->price) {
                $price += $resource->price;
            }
        }
        return $price;
    }

    public function customSlugify($text, string $divider = '-')
    {
        $slug = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $slugCount = Product::where('slug', $slug)->orWhere('slug', 'like', $slug . '%')->count();

        if (empty($slugCount)) {
            return $slug;
        }

        return $slug . $divider . $slugCount;
    }

    private function calculateDiscountedPrice($price, $discountType, $amount)
    {
        if ($price === null) {
            return null;
        }

        if ($discountType === DiscountType::PERCENTAGE) {
            return max(0, $price - ($price * ($amount / 100)));
        }

        if ($discountType === DiscountType::FIXED_RATE || $discountType === 'fixed') {
            return max(0, $price - $amount);
        }

        return $price;
    }

    private function resolveFlashSale($flashSaleId, $product, $hasFlashSale)
    {
        if (!$hasFlashSale) {
            return null;
        }

        if (!empty($flashSaleId)) {
            return FlashSale::find($flashSaleId);
        }

        if ($product instanceof Product) {
            return $product->flash_sales()->orderBy('start_date', 'desc')->first();
        }

        return null;
    }

    private function calculateFlashSalePrice($flashSale, $basePrice)
    {
        if (!$flashSale || $basePrice === null) {
            return null;
        }

        return $flashSale->calcPrice($basePrice);
    }
}
