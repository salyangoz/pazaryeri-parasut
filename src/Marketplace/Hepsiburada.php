<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;
use salyangoz\pazaryeriparasut\Models\Order;
use Carbon\Carbon;

class Hepsiburada extends Marketplace
{

	private $marketplace = "Hepsiburada";
	
	public function __construct(array $config)
	{
        parent::__construct($config);

		$hbConfig	=	[
			'username'		=>array_get($config, 'hepsiburada_username'),
			'password'		=>array_get($config, 'hepsiburada_password'),
			'order_endpoint'=>array_get($config, 'hepsiburada_order_endpoint'),
			'merchant_id'	=>array_get($config, 'hepsiburada_merchant_id')
		];
		
		$this->hepsiburada	    =	new pazaryeriparasut\Library\Hepsiburada($hbConfig);
	}

    /**
     *
     */
	public function pull()
	{
		$orders = $this->sales();

        foreach ($orders as $order)
        {
            $this->processSale($order);
        }
	}

    /**
     * @return mixed
     */
	protected function sales()
    {
        return $this->hepsiburada->orders();
    }

    /**
     * @param $sale
     */
    protected function processSale($sale)
    {

        /** Ürün Unpacket durumundaysa atlıyor */
        if($sale->status == "Unpacked")
        {
            return;
        }

        $orderID = $sale->items[0]->orderNumber;

        /**
         * Sipariş daha önce işlendiyse atlıyor
         */
        $orderCount = Order::where('marketplace', $this->marketplace)
            ->where(function($q) use ($sale, $orderID) {
                $q->where('order_id',$sale->packageNumber)
                    ->orWhere('order_id', $orderID);
            })
            ->count();
        if($orderCount>0)
        {
            return;
        }

        $contactType        =   "Customer";
        if($sale->taxOffice)
        {
            $contactType    =   "Company";
        }

        $tc = self::fillTc($sale->identityNo);

        $taxNumber  =   $sale->taxNumber;

        $address    =   $sale->billingAddress." ".$sale->billingDistrict." / ".$sale->billingCity;

        list($date) = explode(".",$sale->items[0]->orderDate);

        $orderDate  = Carbon::createFromFormat("Y-m-d\\TH:i:s", $date);

        $pull   =   new pazaryeriparasut\Services\Pull($this->marketplace);
        $pull->createCustomer($contactType,
            $sale->customerId, $sale->companyName, $address,$taxNumber,
            $sale->taxOffice, $sale->billingCity, $sale->billingDistrict, $sale->phoneNumber, $sale->email, $tc, $sale->recipientName);

        $pull->createOrder($orderID, $this->getTotal($sale->items), "HB #" . $orderID, $orderDate);

        foreach ($sale->items as $product)
        {
            $pull->addProduct($product->productName, $product->listingId, $product->quantity, $product->price->amount);
        }

    }

    /**
     * @param $items
     * @return string
     */
    private function getDescription($items)
    {
        $description    =   "";
        foreach($items as $item)
        {
            $description.=$item->productName." ";
        }

        return $description;
    }

    /**
     * @param $items
     * @return int
     */
    private function getTotal($items)
    {
        $total = 0;
        foreach ($items as $item)
        {
            $total+=($item->totalPrice->amount);
        }

        return $total;
    }
}