<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

use Parasut\Client as ParasutClient;

abstract class MarketPlace
{
    protected $parasut;
    protected $localStorage;
    protected $config;
    protected $parasutAdapter;

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

    abstract protected function sales();

    abstract protected function processSale($sale);

    abstract public function transfer();
}