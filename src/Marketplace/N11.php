<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

class N11 extends Marketplace
{
    private $n11;

    public function __construct(array $config)
    {

        parent::__construct($config);

        $n11Params = ['appKey'       =>  array_get($config,'n11_app_key'),
                      'appSecret'    =>  array_get($config,'n11_app_secret'),
                      'baseUrl'      =>  array_get($config,'n11_base_url')];

        $this->n11  = new pazaryeriparasut\Library\N11($n11Params);

    }

    /**
     * n11 son 1 gün içerisindeki Siparişlerini getirir
     * @return array
     */
    public function sales()
    {
        $sales = [];

        try{
            $orderList  = $this->n11->DetailedOrderList(
                [
                    "productId"=>'',
                    "status"=> 'Approved',
                    "buyerName"=> '',
                    "orderNumber"=> '',
                    "productSellerCode" =>'',
                    "recipient"=> '',
                    "period"=>[
                        "startDate"=> date_create('-5 day')->format('d/m/Y'),
                        "endDate"=> date_create('now')->format('d/m/Y')
                    ]
                ]
            );

            $this->n11->checkResponse($orderList);
			
			if(!isset($orderList->orderList->order))
				return [];

            foreach($orderList->orderList->order as $order){

                $orderDetail  = $this->n11->OrderDetail([
                    "id" => $order->id
                ]);

                $sales[]    =   $orderDetail;
            }

            return $sales;


        }catch(Exception $ex){
            return [];
        }
    }

    /**
     * n11 Satışlarını paraşüte aktarır
     */
    public function transfer()
    {
        $sales = $this->sales();

        foreach ($sales as $sale)
        {
            $this->process_sale($sale);

        }
    }

    private function process_sale($sale)
    {

        $this->parasutAdapter   =   new pazaryeriparasut\ParasutAdapter($this->config,"N11");
        $sale = $sale->orderDetail;

        /** Eğer fatura daha önce işlendiyse atlıyor */
        if($this->localStorage->get("order.N11_".$sale->id))
        {
            return;
        }

        /** Eğer sipariş tutarı 0 tl ise atıyor */
        if($sale->billingTemplate->dueAmount == 0 )
            return;

        $contactType    =   $sale->buyer->taxId?'company':'person';

        $taxNumber = $sale->buyer->taxId;

        $taxOffice =    $sale->buyer->taxOffice;
        if(!$taxNumber)
        {
            $taxNumber = $sale->buyer->tcId?$sale->buyer->tcId:11111111111;
        }
        else
        {
            $taxOffice =    $sale->buyer->taxOffice ? $sale->buyer->taxOffice : $sale->billingAddress->district;
        }

        $this->parasutAdapter->setContact(
            $contactType,$sale->buyer->id,$sale->billingAddress->fullName,
            $sale->billingAddress->address,$taxNumber,$taxOffice,$sale->billingAddress->city,$sale->billingAddress->district,
            $sale->billingAddress->gsm,$sale->buyer->email
        );

        $items  =   [];
        foreach ($sale->itemList as $item1)
        {

            if(!is_array($item1)){
                $items[] = $item1;
            }
            else
            {
                $items  =   $item1;
            }

            $invoiceDescription =   "";

            $total  =   0;

            foreach ($items as $i)
            {
                $this->parasutAdapter->addProduct($i->productName,$i->productId,$i->quantity,($i->dueAmount / $i->quantity));
                $invoiceDescription.=$i->productName." ";
                $total+=$i->dueAmount/$i->quantity;

            }

        }

        $this->parasutAdapter->saveInvoice($sale->id,$sale->billingTemplate->dueAmount,$invoiceDescription,date('Y-m-d'));

    }

}