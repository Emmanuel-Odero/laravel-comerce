<?php

namespace Webkul\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Models\ProductProxy;
use Webkul\Product\Models\ProductFlatProxy;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Checkout\Database\Factories\CartItemFactory;
use Webkul\Checkout\Contracts\CartItem as CartItemContract;


class CartItem extends Model implements CartItemContract
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $casts = [
        'additional' => 'array',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(ProductProxy::modelClass(), 'id', 'product_id');
    }

    /**
     * The Product Flat that belong to the product.
     */
    public function product_flat()
    {
        return (ProductFlatProxy::modelClass())::where('product_flat.product_id', $this->product_id)
                                               ->where('product_flat.locale', app()->getLocale())
                                               ->where('product_flat.channel', core()->getCurrentChannelCode())
                                               ->select('product_flat.*');
    }

    /**
     * Get all the attributes for the attribute groups.
     */
    public function getProductFlatAttribute()
    {
        return $this->product_flat()
                    ->first();
    }

    public function cart(): HasOne
    {
        return $this->hasOne(CartProxy::modelClass(), 'id', 'cart_id');
    }

    /**
     * Get the child item.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(static::class, 'id', 'parent_id');
    }

    /**
     * Get the parent item record associated with the cart item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the children items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Create a new factory instance for the model
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return CartItemFactory::new();
    }
}
