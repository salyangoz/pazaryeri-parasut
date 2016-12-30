<?php

namespace salyangoz\pazaryeriparasut\Models;

use Illuminate\Database\Eloquent\Model;
use salyangoz\pazaryeriparasut\Models\Order;

class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer';

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
    protected $fillable = ['marketplace','customer_id','type', 'name',
        'invoice_address','city','district','tc','tax_number','tax_office','parasut_id','phone','email'];


    public function order()
    {
        return $this->hasMany('salyangoz\pazaryeriparasut\Models\Order');
    }
}
