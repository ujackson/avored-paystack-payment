<?php

namespace Ujackson\AvoredPaystack\Models;

use AvoRed\Framework\Database\Models\Order;
use Illuminate\Database\Eloquent\Model;

class PaystackTransaction extends Model
{
    protected $fillable = ['transaction_id', 'transaction_ref', 'transaction_amount', 'order_id'];

    /**
     * Transaction belongs to one order.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
