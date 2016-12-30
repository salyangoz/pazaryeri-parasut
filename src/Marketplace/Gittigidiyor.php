<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use Exception;
use salyangoz\pazaryeriparasut;
use salyangoz\pazaryeriparasut\Models\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Gittigidiyor extends Marketplace
{

    private $gittigidiyor;
    private $marketplace =   "Gittigidiyor";

    public function __construct(array $config)
    {
        $gittigidiyorConfig =   [
            'apiKey'            =>  array_get($config, 'gittigidiyor_api_key'),
            'secretKey'         =>  array_get($config, 'gittigidiyor_secret_key'),
            'nick'              =>  array_get($config, 'gittigidiyor_username'),
            'password'          =>  array_get($config, 'gittigidiyor_password'),
            'auth_user'         =>  array_get($config, 'gittigidiyor_auth_user'),
            'auth_pass'         =>  array_get($config, 'gittigidiyor_auth_password'),
            'lang'              =>  array_get($config, 'gittigidiyor_lang'),
            'developer_base_url'=>  array_get($config, 'gittigidiyor_developer_base_url'),
            'product_base_url'  =>  array_get($config, 'gittigidiyor_product_base_url')
        ];
        $this->gittigidiyor =   new pazaryeriparasut\Library\Gittigidiyor($gittigidiyorConfig);
    }

    /**
     * Bir satışı işler
     * @param $sale
     */
    protected function processSale($sale)
    {

		$orderCount = Order::where('marketplace',$this->marketplace)->where('order_id',$sale->saleCode)->count();

        if($orderCount>0)
        {
            return;
        }
        /**
        $format = "d/m/Y H:i:s";
        $date = \DateTime::createFromFormat($format, $sale->lastActionDate);

        if(!($date->getTimestamp() >= strtotime("12/01/2016 00:01")))
        {
            return;
        }
*       */
        /** Sipari tutarı 0 tl ise atlıyor */
        if($sale->price == 0)
            return;

        $contactType    =   "Customer";

        $tax        =   null;
        $tc         =   null;

        if(isset($sale->invoiceInfo))
        {
            $address    =   $sale->invoiceInfo->address;

            if(isset($sale->invoiceInfo->taxNumber))
                $tax        =   $sale->invoiceInfo->taxNumber;
            else
            {
                if(isset($sale->invoiceInfo->tcCertificat))
                {
                    $tc        =   self::fillTc($sale->invoiceInfo->tcCertificate);
                }
            }

            if(isset($sale->invoiceInfo->taxOffice))
            {
                $taxOffice  =   $sale->invoiceInfo->taxOffice ? $sale->invoiceInfo->taxOffice : $sale->buyerInfo->district;
            }
            else
            {
                $taxOffice  =    $sale->buyerInfo->district;
            }
            if(isset($sale->invoiceInfo->taxOffice))
            {
                if($sale->invoiceInfo->taxOffice)
                    $contactType    =   "Company";
            }

            $fullname   =   $sale->invoiceInfo->companyTitle ? $sale->invoiceInfo->companyTitle : $sale->invoiceInfo->fullname;
            $district   =   $sale->invoiceInfo->district;
            $phone      =   $sale->invoiceInfo->phoneNumber;
        }
        else
        {
            $address    =   $sale->buyerInfo->address;
            $tc         =   self::fillTc("");
            $district   =   $sale->buyerInfo->district;
            $phone      =   $sale->buyerInfo->phone;
            $taxOffice  =   "";
            $fullname   =   $sale->buyerInfo->name." ".$sale->buyerInfo->surname;
        }

        $pull   =   new pazaryeriparasut\Pull($this->marketplace);

        $pull->createCustomer($contactType,$sale->buyerInfo->username,
                                $fullname,
                                $address,
                                $tax,
                                $taxOffice,
                                $sale->buyerInfo->city,
                                $district,
                                $phone,
                                $sale->buyerInfo->email,
                                $tc
        )

            ->createOrder($sale->saleCode,$sale->price,"GG ".$sale->productTitle, Carbon::now())
            ->addProduct($sale->productTitle,$sale->productId,$sale->amount,$sale->price / $sale->amount);
    }

    protected function sales($page=1)
    {
        $sales = $this->gittigidiyor->getPagedSales(true, 'S', '', 'A', 'D', $page);

        try
        {

            if(!is_array($sales->sales->sale))
            {
                $saleList = $sales->sales;
            }
            else
            {
                $saleList = $sales->sales->sale;
            }

            Log::info('Order Count Per Page:'.count($sales->sales->sale));

            foreach ($saleList as $sale)
            {
                $this->processSale($sale);
            }

            $page++;

        }
        catch (Exception $e)
        {
            Log::info($e->getMessage());
        }

        if($sales->nextPageAvailable)
            return $this->sales($page);
    }

    public function pull()
    {
        return $this->sales();
    }
}