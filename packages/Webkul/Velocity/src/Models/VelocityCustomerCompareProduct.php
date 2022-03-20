<?php

namespace Webkul\Velocity\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Models\ProductFlatProxy;
use Webkul\Customer\Models\CustomerProxy;
use Webkul\Velocity\Contracts\VelocityCustomerCompareProduct as VelocityCustomerCompareProductContract;

class VelocityCustomerCompareProduct extends Model implements VelocityCustomerCompareProductContract
{
    protected $guarded = [];

    /**
     * The product_flat that belong to the compare product.
     */
    public function product_flat()
    {
        return $this->belongsTo(ProductFlatProxy::modelClass(), 'product_flat_id');
    }

    /**
     * The customer that belong to the compare product.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'customer_id');
    }
}