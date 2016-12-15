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
		$this->username = array_get($config,'username');
		$this->password	= array_get($config,'password');
		$this->merchantID	=	array_get($config,'merchant_id');
		$this->orderEndpoint	=	array_get($config,'order_endpoint');
	}
	
	public function sales()
	{
		$client = new GuzzleHttp\Client();
		$res = $client->request('GET', $this->orderEndpoint."packages/merchantid/{$this->merchantID}",
			['auth' => [$this->username,$this->password]]
		);
		
		echo $res->getBody();
	}
}