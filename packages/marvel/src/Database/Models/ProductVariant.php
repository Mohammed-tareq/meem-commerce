<?php

namespace Marvel\Database\Models;

use Marvel\Services\Pricing\ProductPricingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductVariantFactory;



class ProductVariant extends Model
{
    use HasFactory;

    protected $appends = ['current_price', 'sale_price', 'final_price'];

    protected static function newFactory()
    {
        return ProductVariantFactory::new();
    }

    protected $table = 'product_variants';
    protected $fillable = ['price', 'sale_price', 'stock_quantity', 'quantity', 'reserved_quantity', 'sold_quantity', 'height', 'width', 'length', 'weight', 'product_id', 'in_stock'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->getSalePriceAttribute();
    }

    public function getFinalPriceAttribute()
    {
        return $this->getSalePriceAttribute();
    }

    public function getSalePriceAttribute()
    {
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->with('flash_sales')->first();

        if (!$product) {
            return $this->price;
        }

        return app(ProductPricingService::class)->calculateVariantCurrentPrice($product, $this);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, (int) $this->stock_quantity - (int) ($this->reserved_quantity ?? 0));
    }

    public function getQuantityAttribute(): int
    {
        return $this->available_stock;
    }

    public function setQuantityAttribute($value): void
    {
        $this->attributes['stock_quantity'] = (int) $value;
    }
}
