<?php

namespace salyangoz\pazaryeriparasut\Services;

use salyangoz\pazaryeriparasut\Models\Customer;
use salyangoz\pazaryeriparasut\Models\Product;
use salyangoz\pazaryeriparasut\Models\Order;
use DB;
use Carbon\Carbon;

class Pull
{

    private $marketplace;
    private $customer;
    private $order;

    public function __construct($marketplace)
    {
        $this->marketplace  =   $marketplace;
    }

    private function createProduct($productName,$productID)
    {
        $product =  $this->getProduct($productID);

        if(!$product)
        {
            $product = new Product();
            $product->fill(
                [
                    'name'=>$productName,
                    'product_id'=>$productID,
                    'marketplace'=>$this->marketplace
                ]
            );

            $product->save();

            return $product;
        }

        return $product;
    }

    public function createCustomer($contactType,$customerID,$fullName,$address,$taxID,$taxOffice,$city,$district,$phone,$email,$tc)
    {
        $this->customer = $this->getCustomer($customerID);

        if(!$this->customer){
            $customer   =   new Customer();
        }
        else
        {
            $customer   =   $this->customer;
        }

        $customer->fill([
            "marketplace"       => $this->marketplace,
            "customer_id"       => $customerID,
            "type"              => $contactType,
            "invoice_address"   => $address,
            "name"              => $fullName,
            "city"              => $city,
            "district"          => $district,
            "tc"                => $tc,
            "tax_number"        => $taxID == '' ? null : $taxID,
            "tax_office"        => $taxOffice,
            "phone"             => $phone,
            "email"             => $email
        ]);

        $customer->save();

        $this->customer = $customer;

        return $this;
    }

    public function addProduct($productName,$productID,$quantity,$amount)
    {
        $product = $this->getProduct($productID);

        if(!$product)
        {
            $product = $this->createProduct($productName,$productID);
        }

        $this->order->orderProduct()->create([
            'product_id'=>$product->id,
            'quantity'=>$quantity,
            'price'=>$amount
        ]);

        return $this;
    }

    public function createOrder($saleID,$total,$description,$createdAt=false)
    {

        if(!$createdAt)
        {
            $createdAt = Carbon::now()->resetToStringFormat();
        }

        $this->order = $this->getOrder($saleID);

        if(!$this->order){

            $this->order = $this->customer->order()->create(
                [   'marketplace'=>$this->marketplace,
                    'order_id'=>$saleID,
                    'e_invoice_status'=>"waiting",
                    "description"=>$description,
                    'amount'=>$total,
                    'order_created_at'=>$createdAt
                ]);
        }

        return $this;
    }

    private function getCustomer($customerID)
    {
        return Customer::where('marketplace',$this->marketplace)->where('customer_id',$customerID)->first();
    }

    private function getOrder($orderID)
    {
        return Order::where('marketplace',$this->marketplace)->where('order_id',$orderID)->first();
    }

    private function getProduct($productID)
    {
        return Product::where('marketplace',$this->marketplace)->where('product_id',$productID)->first();
    }
}
