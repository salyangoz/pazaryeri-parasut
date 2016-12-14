<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

use Parasut\Client as ParasutClient;

abstract class MarketPlace
{
    protected $parasut;
    protected $localStorage;
    protected $config;

    public function __construct(array $config)
    {
        $this->config   =   $config;
        // create a new client instance
        $this->parasut = new ParasutClient([
            'client_id'     => array_get($config,'parasut_client_id'),
            'client_secret' => array_get($config,'parasut_client_secret'),
            'username'      => array_get($config,'parasut_username'),
            'password'      => array_get($config,'parasut_password'),
            'company_id'    => array_get($config,'parasut_company_id'),
            'grant_type'    => 'password',
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob',
        ]);

        $this->parasut->authorize();

        $this->localStorage =   new pazaryeriparasut\LocalStorage();

    }

    public function sales(){}

    public function transfer(){}
    private function contact($sale){}
    private function invoiceDescription($sale){}
    private function product($item){}

    protected function createContact($customerDetails)
    {

        return $this->parasut->make('contact')->create(
            [
                'name' => array_get($customerDetails['billing'], 'title'),
                'email' => $customerDetails['email'],
                'tax_number' => array_get($customerDetails['billing'], 'number'),
                'tax_office' => array_get($customerDetails['billing'], 'office'),
                'category_id' => null,
                'address_attributes' => [
                    'address' => array_get($customerDetails['billing'], 'address'),
                    'phone' => $customerDetails['phone'],
                    'fax' => null,
                ],
                'city'     =>array_get($customerDetails['billing'],'city'),
                'district' =>array_get($customerDetails['billing'],'district')
            ]
        );
    }
}