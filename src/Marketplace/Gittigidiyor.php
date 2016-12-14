<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

class Gittigidiyor extends Marketplace
{
    private $gittigidiyor;


    public function __construct(array $config)
    {
        parent::__construct($config);

        $gittigidiyorConfig =   [
            'apiKey'            =>  array_get($config,'gittigidiyor_api_key'),
            'secretKey'         =>  array_get($config,'gittigidiyor_secret_key'),
            'nick'              =>  array_get($config,'gittigidiyor_username'),
            'password'          =>  array_get($config,'gittigidiyor_password'),
            'auth_user'         =>  array_get($config,'gittigidiyor_auth_user'),
            'auth_pass'         =>  array_get($config,'gittigidiyor_auth_password'),
            'lang'              =>  array_get($config,'gittigidiyor_lang'),
            'developer_base_url'=>  array_get($config,'gittigidiyor_developer_base_url'),
            'product_base_url'  =>  array_get($config,'gittigidiyor_product_base_url')
        ];

        $this->gittigidiyor =   new pazaryeriparasut\Library\Gittigidiyor($gittigidiyorConfig);
    }

    private function processSale($sale)
    {
        $parasutAdapter =   new pazaryeriparasut\ParasutAdapter($this->config,"GG");

        if(isset($sale->invoiceInfo))
        {
            $address    =   $sale->invoiceInfo->address;
            $tax        =   $sale->invoiceInfo->taxNumber ? $sale->invoiceInfo->taxNumber :  $sale->invoiceInfo->tcCertificate;
            $district   =   $sale->invoiceInfo->district;
            $phone      =   $sale->invoiceInfo->phoneNumber;
            $taxOffice  =   $sale->invoiceInfo->taxOffice ? $sale->invoiceInfo->taxOffice : $sale->buyerInfo->district;
        }
        else
        {
            $address    =   $sale->buyerInfo->address;
            $tax        =   "";
            $district   =   $sale->buyerInfo->district;
            $phone      =   $sale->buyerInfo->phone;
            $taxOffice  =   $district;
        }

        $parasutAdapter->setContact($sale->buyerInfo->username,
            $sale->buyerInfo->name." ".$sale->buyerInfo->surname,
            $address,
            $tax,
            $taxOffice,
            $sale->buyerInfo->city,
            $district,
            $phone,
            ""
            );


        $parasutAdapter->addProduct($sale->productTitle,$sale->productId,$sale->amount,$sale->price);

        $parasutAdapter->saveInvoice($sale->saleCode,$sale->price,$sale->productTitle,str_replace("/","-",$sale->lastActionDate));
    }

    private function process($page=1)
    {

        $sales = $this->gittigidiyor->getPagedSales(true, 'S', '', 'A', 'D', $page);

        foreach (array_reverse($sales->sales->sale) as $sale)
        {
            $saleTime = \DateTime::createFromFormat("d/m/Y H:i:s",$sale->lastActionDate)->getTimestamp();
            /**
             * Apide tarih filtreleme olmadığı için, siparişin son etkinlik tarihine göre kontrol ediliyor. Son hareket bugün olanları baz alıyor.
             */
            if($saleTime > strtotime("today"))
            {
                $this->processSale($sale);
            }

        }

        if($sales->nextPageAvailable)
            return $this->process($page++);
    }

    public function transfer()
    {
        $this->process();
    }
}