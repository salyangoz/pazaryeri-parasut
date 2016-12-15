<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

class Hepsiburada extends Marketplace
{
	
	private $hepsiburada;
	
	public function __construct(array $config)
	{
        parent::__construct($config);

		$hbConfig	=	[
			'username'		=>array_get($config,'hepsiburada_username'),
			'password'		=>array_get($config,'hepsiburada_password'),
			'order_endpoint'=>array_get($config,'hepsiburada_order_endpoint'),
			'merchant_id'	=>array_get($config,'hepsiburada_merchant_id')
		];
		
		$this->hepsiburada	    =	new pazaryeriparasut\Library\Hepsiburada($hbConfig);
	}
	
	public function transfer()
	{
		$orders = $this->hepsiburada->orders();
        foreach ($orders as $order)
        {
            $this->process_sale($order);
        }
	}

	private function process_sale($sale)
    {
        $this->parasutAdapter   =   new pazaryeriparasut\ParasutAdapter($this->config,"HB");

        /**
         * Sipariş daha önce işlendiyse atlıyor
         */
        if($this->localStorage->get('order.HB_'.$sale->id))
            return;

        $contactType    =   "person";
        if($sale->taxOffice)
            $contactType    =   "company";

        if($sale->taxNumber)
            $taxNumber  =   $sale->taxNumber;
        else{
            $taxNumber  =   $sale->identityNo;
            if(!$taxNumber) $taxNumber  =   11111111111;
        }

        $this->parasutAdapter->setContact($contactType,$sale->customerId,$sale->companyName,
            $sale->billingAddress." ".$sale->billingDistrict." / ".$sale->billingCity,
            $taxNumber,$sale->taxOffice,$sale->billingCity,$sale->billingDistrict,$sale->phoneNumber,$sale->email
            );

        $total = 0;
        $description    =   "";

        foreach ($sale->items as $product)
        {
            $this->parasutAdapter->addProduct($product->productName,$product->lineItemId,$product->quantity,$product->price->amount);
            $total+=($product->price->amount*$product->quantity);

            $description    =   $product->productName." ";
        }

        $this->parasutAdapter->saveInvoice($sale->id,$total,$description,date('Y-m-d'));

    }
}