<?php

namespace Salyangoz\pazaryeriparasut\Library;

use GuzzleHttp;

class Hepsiburada
{
	private $username;
	private $password;
	private $orderEndpoint;
	private $merchantID;
    private $authentication;
    private $guzzleClient;
	
	public function __construct(array $config)
	{
		$this->username         =   array_get($config,'username');
		$this->password	        =   array_get($config,'password');
		$this->merchantID	    =   array_get($config,'merchant_id');
		$this->orderEndpoint	=	array_get($config,'order_endpoint');

        $this->authentication   =   ['auth' => [$this->username,$this->password],'verify' => false];
        $this->guzzleClient = new GuzzleHttp\Client();
	}

    /**
     * Son 120 saate gerçekeşen “unpacked” ve “open” durumundaki siparişleri döndürür
     * @return mixed
     */
	public function orders()
	{
		$res = $this->guzzleClient->request('GET', $this->orderEndpoint."packages/merchantid/{$this->merchantID}?timespan=168",
			$this->authentication
		);
		
		return GuzzleHttp\json_decode($res->getBody());
	}

    /**
     * Paket durumunu döndürür
     * @param $orderID
     * @return mixed
     */
	public function packageStatus($orderID)
    {
        $res = $this->guzzleClient->request('GET', $this->orderEndpoint."packages/merchantid/{$this->merchantID}/packagenumber/{$orderID}",
            $this->authentication
        );

        return GuzzleHttp\json_decode($res->getBody());
    }
}