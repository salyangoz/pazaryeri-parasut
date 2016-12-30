<?php

namespace salyangoz\pazaryeriparasut\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'order';

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
    protected $fillable = ['marketplace','order_id','customer_id',
                            'e_invoice_status','e_invoice_document_type',
                            'e_invoice_url','amount','parasut_id','description',
                            'e_invoice_at','order_created_at'];

    public function scopeWaiting($query)
    {
        return $query->whereNull("parasut_id");
    }

    public function scopeWaitingEinvoice($query)
    {
        return $query->whereNotNull('parasut_id')->where('e_invoice_status','waiting');
    }

    public function scopeAvibleEinvoices($query)
    {
        return $query->whereNotNull('parasut_id')->where('e_invoice_status','request_sent');
    }

    public function customer()
    {
        return $this->belongsTo('salyangoz\pazaryeriparasut\Models\Customer');
    }

    public function orderProduct()
    {
        return $this->hasMany('salyangoz\pazaryeriparasut\Models\OrderProduct');
    }
}
