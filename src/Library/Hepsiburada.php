<?php

namespace Salyangoz\pazaryeriparasut\Library;

use GuzzleHttp;

class Hepsiburada
{
	private $username;
	private $password;
	private $orderEndpoint;
	private $merchantID;
	
	public function __construct(array $config)
	{
		$this->username         =   array_get($config,'username');
		$this->password	        =   array_get($config,'password');
		$this->merchantID	    =   array_get($config,'merchant_id');
		$this->orderEndpoint	=	array_get($config,'order_endpoint');
	}

    /**
     * Son 120 saate gerçekeşen “unpacked” ve “open” durumundaki siparişleri döndürür
     * @return mixed
     */
	public function orders()
	{
		$client = new GuzzleHttp\Client();
		$res = $client->request('GET', $this->orderEndpoint."packages/merchantid/{$this->merchantID}?timespan=120",
			['auth' => [$this->username,$this->password],'verify' => false]
		);
		
		return GuzzleHttp\json_decode($res->getBody());
	}
}