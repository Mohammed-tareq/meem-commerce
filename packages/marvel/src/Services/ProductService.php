<?php

namespace Marvel\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVaraint;
use Marvel\Database\Models\AttributeProduct;

class ProductService
{
    public function createProduct(array $productData, array $variants = [], array $images = [])
    {
        try {
            DB::beginTransaction();

            $productData['slug'] = Str::slug($productData['name']['en'] ?? $productData['name'] ?? Str::random(8));
            $product = Product::create($productData);

            if (!$product) {
                DB::rollBack();
                return false;
            }

            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $variant['product_id'] = $product->id;
                    $productVariant = ProductVaraint::create($variant);
                    if (!$productVariant) {
                        DB::rollBack();
                        return false;
                    }

                    if (!empty($variant['attribute_values'])) {
                        foreach ($variant['attribute_values'] as $attributeValueId) {
                            $created = AttributeProduct::create([
                                'product_variant_id' => $productVariant->id,
                                'attribute_value_id' => $attributeValueId,
                            ]);
                            if (!$created) {
                                DB::rollBack();
                                return false;
                            }
                        }
                    }
                }
            }

            // Attach images - supports UploadedFile instances, local file paths, or public URLs
            if (!empty($images)) {
                try {
                    if ($product->hasMedia('products')) {
                        $product->clearMediaCollection('products');
                    }
                    foreach ($images as $img) {
                        if ($img instanceof UploadedFile) {
                            $fileName = (string) Str::uuid() . '.' . $img->getClientOriginalExtension();
                            $product->addMedia($img)->usingFileName($fileName)->toMediaCollection('products', 'products');
                        } elseif (is_string($img) && (filter_var($img, FILTER_VALIDATE_URL))) {
                            $product->addMediaFromUrl($img)->toMediaCollection('products', 'products');
                        } elseif (is_string($img) && file_exists($img)) {
                            $product->addMedia($img)->toMediaCollection('products', 'products');
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }
    }

    public function updateProduct(Product $product, array $productData, array $variants = [], array $images = [])
    {
        try {
            DB::beginTransaction();

            $productData['slug'] = Str::slug($productData['name']['en'] ?? $productData['name'] ?? $product->slug);
            $updated = $product->update($productData);
            if (!$updated) {
                DB::rollBack();
                return false;
            }

            if (!empty($variants)) {
                ProductVaraint::where('product_id', $product->id)->delete();

                foreach ($variants as $variant) {
                    $variant['product_id'] = $product->id;
                    $productVariant = ProductVaraint::create($variant);
                    if (!$productVariant) {
                        DB::rollBack();
                        return false;
                    }

                    if (!empty($variant['attribute_values'])) {
                        foreach ($variant['attribute_values'] as $attributeValueId) {
                            $created = AttributeProduct::create([
                                'product_variant_id' => $productVariant->id,
                                'attribute_value_id' => $attributeValueId,
                            ]);
                            if (!$created) {
                                DB::rollBack();
                                return false;
                            }
                        }
                    }
                }
            }

            // Update images
            if (!empty($images)) {
                try {
                    if ($product->hasMedia('products')) {
                        $product->clearMediaCollection('products');
                    }
                    foreach ($images as $img) {
                        if ($img instanceof UploadedFile) {
                            $fileName = (string) Str::uuid() . '.' . $img->getClientOriginalExtension();
                            $product->addMedia($img)->usingFileName($fileName)->toMediaCollection('products', 'products');
                        } elseif (is_string($img) && (filter_var($img, FILTER_VALIDATE_URL))) {
                            $product->addMediaFromUrl($img)->toMediaCollection('products', 'products');
                        } elseif (is_string($img) && file_exists($img)) {
                            $product->addMedia($img)->toMediaCollection('products', 'products');
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }
    }
}
