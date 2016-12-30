<?php

namespace salyangoz\pazaryeriparasut\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_product';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id','product_id','quantity','price'];

    public function order()
    {
        return $this->belongsTo('salyangoz\pazaryeriparasut\Models\Order');
    }

    public function product()
    {
        return $this->belongsTo('salyangoz\pazaryeriparasut\Models\Product');
    }
}
