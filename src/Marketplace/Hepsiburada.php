<?php

namespace salyangoz\pazaryeriparasut\Marketplace;

use salyangoz\pazaryeriparasut;

class Hepsiburada extends Marketplace
{
	
	private $hepsiburada;
	
	public function __construct(array $config)
	{
		$hbConfig	=	[
			'username'		=>array_get($config,'hepsiburada_username'),
			'password'		=>array_get($config,'hepsiburada_password'),
			'order_endpoint'=>array_get($config,'hepsiburada_order_endpoint'),
			'merchant_id'	=>array_get($config,'hepsiburada_merchant_id')
		];
		
		$this->hepsiburada	=	new pazaryeriparasut\Library\Hepsiburada($hbConfig);
	}
	
	public function transfer()
	{
		$this->hepsiburada->sales();
	}
}