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
                        "startDate"=> date_create('-1 day')->format('d/m/Y'),
                        "endDate"=> date_create('now')->format('d/m/Y')
                    ]
                ]
            );

            print_r($orderList);
            die();

            $this->n11->checkResponse($orderList);

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
            $sale = $sale->orderDetail;

            /** Eğer fatura daha önce işlendiyse atlıyor */
            if($this->localStorage->get("order.N11_".$sale->id))
            {
                continue;
            }

            $invoiceDescription = $this->invoiceDescription($sale);

            $buyerId = $this->contact($sale);

            $invoiceData    =   [
                'item_type'     => 'invoice',
                'description'   => $invoiceDescription,
                'issue_date'    => str_replace("/","-",$sale->createDate),
                'contact_id'    => $buyerId,
                'invoice_series'=> "N11",
                'category_id'   => config("pazaryeri-parasut.parasut_category_id"),
                'payment_status'=>'paid',
            ];

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

                foreach ($items as $i)
                {
                    $productID  =   $this->product($i);

                    $invoiceData['details_attributes'][] =
                        [
                            'product_id'        => $productID,
                            'quantity'          => $i->quantity,
                            'discount_value'    => 0,
                            'discount_type'     => 'amount',
                            'unit_price'        => ($i->dueAmount / $i->quantity) / (1 + 0.18),
                            'vat_rate'          => 0.18 * 100,
                        ];

                }
            }

            /**
             * Fatura kaydı oluşturuluyor
             */
            $response = $this->parasut->make('sale')->create($invoiceData);

            /**
             * Fatura ödendi yapılıyor ve ödeme belirlenen hesap kasasına kaydediliyor
             */
            $this->parasut->make('sale')->paid(
                $response['sales_invoice']['id'],
                [   "amount"=>$sale->billingTemplate->dueAmount,
                    "date"=>date('Y-m-d'),
                    "account_id"=>Config('pazaryeri-parasut.parasut_account_id')]
            );

            $this->localStorage->set('order','N11_'.$sale->id,$response['sales_invoice']['id']);
            $this->localStorage->save();

        }
    }

    /**
     * Satıştan sipariş müşterisi oluşturup paraşüte aktarır
     * @param $sale
     * @return bool|mixed
     */
    private function contact($sale)
    {

        $parasutCustomer['billing']             =   [];
        $parasutCustomer['billing']['title']    =   $sale->billingAddress->fullName;
        $parasutCustomer['billing']['address']  =   $sale->billingAddress->address;
        $parasutCustomer['billing']['number']   =   $sale->buyer->taxId or $sale->buyer->tcId;
        $parasutCustomer['billing']['office']   =   $sale->buyer->taxOffice;
        $parasutCustomer['billing']['city']     =   $sale->billingAddress->city;
        $parasutCustomer['billing']['district'] =   $sale->billingAddress->district;
        $parasutCustomer['phone']               =   $sale->billingAddress->gsm;
        $parasutCustomer['email']               =   $sale->buyer->email;

        $buyerId = $this->localStorage->get("customer.N11_".$sale->buyer->id);

        if(!$buyerId){
            $contact    = $this->createContact($parasutCustomer);
            $buyerId    =   $contact['contact']['id'];

            $this->localStorage->set('customer',"N11_".$sale->buyer->id,$buyerId);
            $this->localStorage->save();
        }
        else
        {
            return $buyerId;
        }

        return $buyerId;
    }

    /**
     * Fatura açıklamasını getirir, açıklamayı sipariş edilen ürünlerin adlarını birleştirerek oluşturur
     * @param $sale
     * @return string
     */
    private function invoiceDescription($sale)
    {
        $itemNames  =   "";

        foreach($sale->itemList as $item)
        {
            if(is_array($item)){
                foreach ($item as $i)
                {
                    $itemNames  =   $i->productName." | ";
                }
            }
            else
            {
                $itemNames = $item->productName." ";
            }
        }

        return $itemNames;
    }

    /**
     * Sipariş ürününü paraşüte tanımlar, eğer ürün daha önce tanımlandıysa o idyi getirir
     * @param $item
     * @return bool|mixed
     */
    private function product($item)
    {
        if(!$productID = $this->localStorage->get("product.".'N11_'.$item->productId))
        {
            $product = $this->parasut->make('product')->create(["name"=>$item->productName,"code"=>'N11_'.$item->productId]);
            $productID = $product['product']['id'];
            $this->localStorage->set('product',"N11_.$item->productId",$product['product']['id']);
            $this->localStorage->save();
        }

        return $productID;
    }

}