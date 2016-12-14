<?php

namespace Salyangoz\pazaryeriparasut\Library;

/**
 * Gittigidiyor Api Client v2.3
 *
 * GittiGidiyor API, dev.gittigidiyor.com adresi uzerinde 
 * hizmet veren web servisler yardimi ile kurumsal firmalarin 
 * veya bireysel kullanicilarin, GittiGidiyor uzerinde yapabildikleri 
 * hemen hemen tum islemleri otomatik olarak veya toplu halde 
 * yapabilmelerini saglayacak metodlari iceren bir uygulamadir.
 * 
 * Bu client dosyası, yazilimcilarin API servislerine kolay bir sekilde ulasmasi icin
 * Gittigidiyor tarafindan hazirlanmis bir scripttir.
 * 
 * Client'in sorunsuz bir sekilde calismasi icin config.ini
 * dosyasinda bulunan alanlara size verilen bilgileri yaziniz.
 *
 * @category   GG
 * @package    ggClient
 * @copyright  Copyright (c) 2000-2011 GittiGidiyor A.S. (http://www.gittigidiyor.com)
 */

class Gittigidiyor
{
	
	protected $apiKey;
	protected $secretKey;
	protected $nick;
	protected $password;
	protected $lang;													
	protected $sign;													
	protected $time;													
	protected $auth_user;
	protected $auth_pass;
	
	
	/**
	 * Create gg client and configure
	 * 
	 * @param string $requestType
	 * @param string $responseType
	 * @param string $lang
	 */
	public function __construct($ini_array){
		
		$this->apiKey               = $ini_array['apiKey'];
        $this->secretKey            = $ini_array['secretKey'];
        $this->nick                 = $ini_array['nick'];
		$this->password             = $ini_array['password'];
		$this->auth_user            = $ini_array['auth_user'];
		$this->auth_pass            = $ini_array['auth_pass'];
        $this->lang                 = $ini_array['lang'];
        $this->product_base_url     = $ini_array['product_base_url'];
        $this->developer_base_url   = $ini_array['developer_base_url'];
		
		list($usec, $sec) = explode(" ", microtime());
		$this->time = round(((float)$usec + (float)$sec) * 100).'0';
		
		$this->sign = md5($this->apiKey.$this->secretKey.$this->time);	
		
	}
	
	
	
	//Start Application Service
	
	/**
	 * API kullanıcisinin sisteme kendini gelistirici olarak kaydettirmesi icin cagirmasi gereken metoddur.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/registerDeveloper-DeveloperService-soap-anonymous
	 * 
	 * Developer Service 
	 * Register Developer Method
	 * 
	 * @param string $nick
	 * @param string $password
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function registerDeveloper($nick = null, $password = null) {
		return $this->clientConnect('anonymous','Developer','registerDeveloper',get_defined_vars());
	}
	
	
	/**
	 * API kullanicisinin sisteme kayitli olup olmadigini ogrenmek icin cagirmasi gereken metoddur.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/isDeveloper-DeveloperService-soap-anonymous
	 * 
	 * Developer Service
	 * Is Developer Method
	 * 
	 * @param string $nick
	 * @param string $password
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function isDeveloper($nick = '',$password = ''){
		return $this->clientConnect('anonymous','Developer','isDeveloper',get_defined_vars());
	}
	
	
	/**
	 *  API kullanicisinin gelistirici anahtarini hatirlayamadigi durumlarda cagirmasi gereken metoddur.
	 * 
	 * Developer Service
	 * Is Developer Method
	 * 
	 * @param string $nick
	 * 
	 * @return integer developer id
	 */
	public function getDeveloperId(){
		$parameters = array('nick'=>$this->nick,'password' => $this->password);
		$result = $this->clientConnect('anonymous','Developer','isDeveloper',$parameters);
		return $result->developerId;
	}
	
	//End Application Service
	
	
	
	
	
	//Start Developer Service
	
	/**
	 * API kullanicinin kendisini gelistirici olarak sisteme kaydettirmesinin ardindan 
	 * uygulama tanimlayabilmesi icin cagirmasi gereken metoddur. 
	 * Bir gelistirici sahip oldugu gelistirici anahtari ile en fazla 5 tane uygulama yaratabilir.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/createApplication-ApplicationService-soap-anonymous
	 * 
	 * Application Service
	 * Create Aplication Method
	 * 
	 * @param integer $developerId
	 * @param string $applicationName
	 * @param string $description
	 * @param string $accessType
	 * @param string $appType
	 * @param string $descDetail
	 * 
	 * @return Web Servis mesajini object olarak dondurur	
	 */
	public function createApplication($developerId = null, 
									  $applicationName = null, 
									  $description = null, 
									  $accessType = null, 
									  $appType = null, 
									  $descDetail = null){
		return $this->clientConnect('anonymous','Application','createApplication',get_defined_vars());
	}
	
	
	/**
	 * Bir gelistiricinin onceden yaratmis oldugu uygulamalari silmek icin kullanmasi gereken metoddur.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/deleteApplication-ApplicationService-soap-anonymous
	 * 
	 * Application Service
	 * Delete Aplication Method
	 * 
	 * @param string $developerId
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function deleteApplication($developerId = null){
		return $this->clientConnect('anonymous','Application','deleteApplication',get_defined_vars());
	}
	
	
	/**
	 * Gelistiricinin yaratmis oldugu uygulamalarin listelesini almak istediginde, cagirmasi gereken metoddur.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getApplicationList-ApplicationService-soap-anonymous
	 * 
	 * Application Service
	 * List Aplication Method
	 * 
	 * @param string $developerId
	 * 
	 * @return object This developers application list.
	 */
	public function getApplicationList($developerId = null){
		return $this->clientConnect('anonymous','Application','getApplicationList',get_defined_vars());
	}
	
	//End Developer Service
	
	
	
	
	
	//Start Category Service
	
	/**
	 * Kategori kodu ve kategori detay bilgilerine ulasmak icin bu metod kullanilmalidir. 
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getCategories-CategoryService-soap-anonymous
	 * 
	 * Category Service
	 * Get Categories Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * @param string	$withSpecs
	 * 
	 * @return object Category list
	 */
	public function getCategories($startOffset = 0, $rowCount = 2, $withSpecs){
		//$withSpecs = $this->booleanConvert($withSpecs);
		return $this->clientConnect('anonymous','Category','getCategories',get_defined_vars());
	}
	
	
	/**
	 * Kategori bilgilerinde zaman icerisinde degisiklik olabilmektedir.
	 * Sadece degisen kategori bilgilerine ihtiyac duyuldugunda belirtilen bir tarihten 
	 * sonra olan degisiklikler bu metod araciligi ile elde edilebilmektedir 
	 * Burada dikkat edilmesi gereken nokta changeTime parametresinin su an ki zamandan 
	 * buyuk bir degere sahip olmamasi ve gecmisteki bir zamana isaret etmesidir. 
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getModifiedCategories-CategoryService-soap-anonymous
	 * 
	 * Category Service
	 * Get Modified Categories Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * @param time		$changeTime		format => 01/01/2008+00:00:00
	 * 
	 * @return object Modified Category list
	 */
	public function getModifiedCategories($startOffset = 0, $rowCount = 2, $changeTime = '01/01/2008+00:00:00'){
		return $this->clientConnect('anonymous','Category','getModifiedCategories',get_defined_vars());
	}
	
	
	/**
	 * Herhangi bir kategorinin detay bilgisine ihtiyac duyuldugunda cagirilmasi gereken metoddur.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getCategory-CategoryService-soap-anonymous
	 * 
	 * Category Service
	 * Get Category Info Method
	 * 
	 * @param string	$categoryCode
	 * @param string	$withSpecs
	 * 
	 * @return object Selected category info
	 */
	public function getCategory($categoryCode = null, $withSpecs = true){
		//$withSpecs = $this->booleanConvert($withSpecs);
		return $this->clientConnect('anonymous','Category','getCategory',get_defined_vars());
	}
	
	
	/**
	 * Sadece kategori ozelliklerinin alinmasi gerektigi durumda cagirilmasi gereken metoddur. 
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getCategorySpecs-CategoryService-soap-anonymous
	 * 
	 * Category Service
	 * Get Category Specs Info Method
	 * 
	 * @param string	$categoryCode
	 * 
	 * @return object Selected categorys specs
	 */
	public function getCategorySpecs($categoryCode = null){
		return $this->clientConnect('anonymous','Category','getCategorySpecs',get_defined_vars());
	}
	
	
	
	/**
	 * Istenilen kategoriye ait variant seceneklerinin cagirilmasi icin gereken metoddur.
	 * 
	 * Category Service
	 * Get Category Variant Specs
	 * 
	 * @param string $categoryCode
	 * 
	 * @return object Selected category variant specs
	 */
	
	public function getCategoryVariantSpecs($categoryCode = null){
		return $this->clientConnect('anonymous','Category','getCategoryVariantSpecs',get_defined_vars());
	}
	
	
	
	/**
	 * Hangi kategorilerde variant oldugunun ogrenilmesi icin gereken metoddur.
	 * 
	 * Cateogry Service
	 * Get Categories Having Variant Specs
	 * 
	 * @return object Categories
	 */
	public function getCategoriesHavingVariantSpecs(){
		return $this->clientConnect('anonymous','Category','getCategoriesHavingVariantSpecs',get_defined_vars());
	}
	
	
	//End Category Service
	
	
	
	
	
	//Start City Service
		
	/**
	 * Gitti Gidiyor sistemindeki sehir ad ve kodlarini sunan servistir
	 * Şehir kodlari il plaka kodlarindan farkli olabilecegi icin degerlerinin bu servisten kontrol edilmesi gerekmektedir.
	 * 
	 * Detayli Bilgi: http://dev.gittigidiyor.com/metotlar/getCities-CityService-soap-anonymous
	 * 
	 * City Service
	 * Get Cities Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * 
	 * @return object Cities list
	 */
	public function getCities($startOffset = 0, $rowCount = 5){
		return $this->clientConnect('anonymous','City','getCities',get_defined_vars());
	}

	
	/**
	 * Şehir bilgilerinde zaman icerisinde degisiklik olabilmektedir,
	 * Sadece degisen sehir bilgilerine ihtiyac duyuldugunda belirtilen bir tarihten 
	 * sonra olan degisiklikler bu metod araciligi ile elde edilebilmektedir.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getModifiedCities-CityService-soap-anonymous
	 * 
	 * City Service
	 * 
	 * Get Modified Cities Method
	 * 
	 * @param time		$changeTime		format=>01/01/2008+00:00:00
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * 
	 * @return object Modified Cities List
	 */
	public function getModifiedCities($changeTime = '01/01/2008+00:00:00', $startOffset = 0, $rowCount = 5){
		return $this->clientConnect('anonymous','City','getModifiedCities',get_defined_vars());
	}
	
	
	/**
	 * Sistemde kullanilan sehir verilerinden herhangi birisinin detayina 
	 * erisilmek istendigi durumda cagirilmasi gereken metoddur.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getCity-CityService-soap-anonymous
	 * 
	 * City Service
	 * Get City Info Method
	 * 
	 * @param string $code
	 * 
	 * @return object Selected City info
	 */
	public function getCity($code = null){
		return $this->clientConnect('anonymous','City','getCity',get_defined_vars());
	}
	
	//End City Service
	
	
	
	
	
	//Start Product Service
	
	/**
	 * Bir urunun detay bilgisini almak icin bu metod kullanilmalidir.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getProduct-ProductService-soap-individual
	 * 
	 * Product Service
	 * Get Product Info Method
	 * 
	 * @param string	$productId
	 * @param string	$itemId
	 * 
	 * @return object Selected Product info
	 */
	public function getProduct($productId = '',$itemId = ''){
		return $this->clientConnect('individual','Product','getProduct',get_defined_vars());
	}
	
	
	/**
	 * Bu metod farkli durumlara (productStatus) sahip urunlerin listelesini almak icin kullanilmaktadir
	 * “status” parametresi listesi alinmak istenen urunlerin durumunu ifade etmektedir.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getProducts-ProductService-soap-individual
	 * 
	 * Product Service
	 * Get Products Info Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * @param string	$status
	 * @param string	$withData
	 * 
	 * @return object Products List
	 */
	public function getProducts($startOffset = 0, $rowCount = 3, $status = 'A', $withData = false){
		//$withData = $this->booleanConvert($withData);
		return $this->clientConnect('individual','Product','getProducts',get_defined_vars());
	}
	
	
	/**
	 * Urune ait variant bilgilerini dondurur
	 * 
	 * Product Service
	 * Get Product Variants
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * @param string $variantId
	 * @param string $variantStockCode
	 * 
	 * @return object variand data
	 */
	public function getProductVariants($productId = '',$itemId = '',$variantId = '',$variantStockCode = ''){
		return $this->clientConnect('individual','Product','getProductVariants',get_defined_vars());
	}
	
	
	/**
	 * Listeleme servisine urun kaydetmek icin kullanilmasi gereken metoddur.
	 * Detayli Bilgi : http://dev.gittigidiyor.com/servisler/insertProduct-ProductService-soap-individual
	 * 
	 * Product Service
	 * Insert Product Method
	 * 
	 * @param string	$options Icerigi xml olan bu degisken belli bir standarda sahipdir. Daha fazla bilgi icin api document.
	 * @param string	$itemId Kategori bazli spec girisi 
	 * @param string	$nextDateOption Ileri tarihli urun girme islemi
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function insertProducts($itemId = null,$product,$forceToSpecEntry = false,$nextDateOption = false){
		return $this->clientConnect('individual','Product','insertProduct',get_defined_vars(),array('product'));
	}
	
	
	
	/**
	 * Listeleme servisine variant urun kaydetmek icin kullanilmasi gereken metoddur.
	 * 
	 * Product Service
	 * Insert Retail Product Method
	 *
	 * @param string	$options Icerigi xml olan bu degisken belli bir standarda sahipdir. Daha fazla bilgi icin api document.
	 * @param string	$itemId Kategori bazli spec girisi
	 * @param string	$nextDateOption Ileri tarihli urun girme islemi
	 *
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function insertRetailProduct($itemId = null,$product,$forceToSpecEntry = false,$nextDateOption = false){
		return $this->clientConnect('individual','Product','insertRetailProduct',get_defined_vars(),array('product'));
	}
	
	
	
	/**
	 * Belli bir urunun kopyasini almak icin kullanilmasi gereken metoddur.
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/cloneProduct-ProductService-soap-individual
	 * 
	 * Product Service
	 * Clone Product Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * 
	 * @return  Web Servis mesajini object olarak dondurur
	 */
	public function cloneProduct($productId = '',$itemId = ''){
		return $this->clientConnect('individual','Product','cloneProduct',get_defined_vars());
	}
	
	
	/**
	 * Ürun/urunleri silmek icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/deleteProduct-ProductService-soap-individual
	 * 
	 * Product Service
	 * Delete Product Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function deleteProduct($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		
		return $this->clientConnect('individual','Product','deleteProduct',$options,array('productIdList','itemIdList'));
	}
	
	
	
	/**
	 * Ürun/urunleri silmek icin kullanilir. 
	 * Detayli Bilgi : 
	 * 
	 * Product Service
	 * Delete Products Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function deleteProducts($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		
		return $this->clientConnect('individual','Product','deleteProducts',$options,array('productIdList','itemIdList'));
	}
	
	
	
	
	/**
	 * Satılmayan urunleri yeniden listelemek icin kullanilir.
	 * Detayli Bilgi : ?
	 * 
	 * Product Service
	 * ReList Products Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function relistProducts($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		
		return $this->clientConnect('individual','Product','relistProducts',$options,array('productIdList','itemIdList'));
	}
	
	
	
	/**
	 * GittiGidiyor'da "Yeni Listelenenler" bolumundeki urunlere ulasmak icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getNewlyListedProductIdList-ProductService-soap-individual
	 * 
	 * Product Service
	 * getNewlyListedProductIdList Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * @param boolean	$viaApi
	 * 
	 * @return object Yeni Listelenen Ürunler
	 */
	public function getNewlyListedProductIdList($startOffset = 0, $rowCount = 3,$viaApi = true){
		return $this->clientConnect('individual','Product','getNewlyListedProductIdList',get_defined_vars());
	}
	
	
	/**
	 * Satisa cikarilacak urun/urunler icin odenmesi gereken listeleme servisi ucretini ya da satilmayan 
	 * fakat yeniden listelenmesi istenen urun/urunlerin listeleme ucretini yeniden hesaplamak icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/calculatePriceForShoppingCart-ProductService-soap-individual
	 * 
	 * Product Service
	 * calculatePriceForShoppingCart Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return object urun fiyati
	 */
	public function calculatePriceForShoppingCart($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','calculatePriceForShoppingCart',$options,array('productIdList','itemIdList'));
	}
	
	
	/**
	 * Satisa cikarilmis bir urunde yapilacak revizyonun ucretini hesaplamak icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/calculatePriceForRevision-ProductService-soap-individual
	 * 
	 * Product Service
	 * calculatePriceForRevision Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * 
	 * @return object revizyon fiyati
	 */
	public function calculatePriceForRevision($productId = null, $itemId = null){
		return $this->clientConnect('individual','Product','calculatePriceForRevision',get_defined_vars());
	}
	
	
	/**
	 * Listeleme servisi ve revizyon ucretlerini odemek icin kullanilir. 
	 * Kullanici, odeme ceki ve kredi karti bilgilerini kullanarak odeme islemini gerceklestirir.
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/payPrice-ProductService-soap-individual
	 * 
	 * Product Service
	 * Pay Price Method
	 * 
	 * @param string $voucher
	 * @param string $ccOwnerName
	 * @param string $ccOwnerSurname
	 * @param integer $ccNumber
	 * @param integer $cvv
	 * @param integer $expireMonth
	 * @param integer $expireYear
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function payPrice($voucher,$ccOwnerName,$ccOwnerSurname,$ccNumber,$cvv,$expireMonth,$expireYear){
		return $this->clientConnect('individual','Product','payPrice',get_defined_vars());
	}
	
	
	/**
	 * Satistaki urunu erken sonlandirmak icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/finishEarly-ProductService-soap-individual
	 * 
	 * Product Service
	 * finishEarly Method
	 * 
	 * @param array $products
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function finishEarly($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','finishEarly',$options,array('productIdList','itemIdList'));
	}
	
	
	
	/**
	 * Satistaki urunu erken sonlandirmak icin kullanilir. 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/finishEarly-ProductService-soap-individual
	 * 
	 * Product Service
	 * finishEarly Method
	 * 
	 * @param array $products
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function finishEarlyProducts($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','finishEarlyProducts',$options,array('productIdList','itemIdList'));
	}
	
	
	
	/**
	 * Fiyat bilgilerini guncellemek icin kullanilir.  
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/updatePrice-ProductService-soap-individual
	 * 
	 * Product Service
	 * Update Price Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * @param string $price
	 * @param string $cancelBid
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function updatePrice($productId,$itemId,$price,$cancelBid){
		return $this->clientConnect('individual','Product','updatePrice',get_defined_vars());
	}
	
	
	/**
	 * Stok bilgilerini guncellemek icin kullanilir.   
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/updateStock-ProductService-soap-individual
	 * 
	 * Product Service
	 * Update Stock Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * @param string $stock
	 * @param string $cancelBid
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function updateStock($productId,$itemId,$stock,$cancelBid){
		return $this->clientConnect('individual','Product','updateStock',get_defined_vars());
	}
	
	
	
	/**
	 * Urun guncellemek icin kullanilmasi gereken metoddur.
	 * Detayli Bilgi : http://dev.gittigidiyor.com/servisler/updateProduct-ProductService-soap-individual
	 * 
	 * Product Service
	 * Update Product Method
	 *
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function updateProduct($itemId = null, $productId = null, $product, $onSale = true, $forceToSpecEntry = false, $nextDateOption = false){
		return $this->clientConnect('individual','Product','updateProduct',get_defined_vars(),array('product'));
	}
	
	
	/**
	 * Urun variant bilgileirni guncellemek icin kullanılmasi gereken metoddur.
	 * 
	 * Product Service
	 * Update Product Variants
	 * 
	 * @param unknown_type $itemId
	 * @param unknown_type $productId
	 * @param unknown_type $productVariant
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function updateProductVariants($itemId = null, $productId = null, $productVariant){
		return $this->clientConnect('individual','Product','updateProductVariants',get_defined_vars(),array('productVariant'));
	}
	
	
	
	
	/**
	 * Stok ve Fiyat bilgilerini almak icin kullanilir.  
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getStockAndPrice-ProductService-soap-individual
	 * 
	 * Product Service
	 * getStockAndPrice Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function getStockAndPrice($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','getStockAndPrice',$options,array('productIdList','itemIdList'));
	}
	
	
	
	
	/**
	 * Product Service
	 * getProductStatuses Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function getProductStatuses($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','getProductStatuses',$options,array('productIdList','itemIdList'));
	}
	
	
	
	
	/**
	 * Urun ozellikleri bilgilerini almak icin erisilir.  
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getProductSpecs-ProductService-soap-individual
	 * 
	 * Product Service
	 * getProductSpecs Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function getProductSpecs($productId = null, $itemId = null){
		return $this->clientConnect('individual','Product','getProductSpecs',get_defined_vars());
	}
	
	
	
	/**
	 * Product Service
	 * getProductsByIds Method
	 * 
	 * @param array $products
	 * @param array $items
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function getProductsByIds($products = array(),$items = array()){
		$xml = '<productIdList>';
		if (count($products) > 0){
			foreach ($products as $product){
				$xml .= "<item>{$product}</item>";
			}
		}
		$xml .= '</productIdList>';
		$options['productIdList'] = $xml;
		
		$xml = '<itemIdList>';
		if (count($items) > 0){
			foreach ($items as $item){
				$xml .= "<item>{$item}</item>";
			}
		}
		$xml .= '</itemIdList>';
		$options['itemIdList'] = $xml;
		return $this->clientConnect('individual','Product','getProductsByIds',$options,array('productIdList','itemIdList'));
	}
	
	
	
	/**
	 * Urun aciklamasini almak icin erisilir.  
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getProductDescription-ProductService-soap-individual
	 * 
	 * Product Service
	 * getProductDescription Method
	 * 
	 * @param string $productId
	 * @param string $itemId
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function getProductDescription($productId = null, $itemId = null){
		return $this->clientConnect('individual','Product','getProductDescription',get_defined_vars());
	}
	
	
	//End Product Service
	
	
	
	
	
	//Start Sale Service
	
	/**
	 * Satici satis kodunu girmek suretiyle mevcut satisin bilgilerini elde edebilir.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getSale-SaleService-soap-individual
	 * 
	 * Sale Service
	 * 
	 * Get Sale
	 * 
	 * @param integer	$saleCode
	 * 
	 * @return object Sale code info
	 */
	public function getSale($saleCode = null){
		return $this->clientConnect('individual','Sale','getSale',get_defined_vars());
	}	
	
	
	/**
	 * Satici konumundaki kullanici bu metod araciligi ile 
	 * GittiGidiyor Bana Özel alaninin Sattiklarim bolumunde sunulan bilgilerin tamamini elde edebilir, 
	 * bilgiler filtrelenebilir ve de siralanabilir. 
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getSales-SaleService-soap-individual
	 * 
	 * Get Sales
	 * 
	 * @param integer	$saleCode
	 * @param integer	$rowCount
	 * @param string	$withData
	 * @param string	$byStatus
	 * @param string	$byUser
	 * @param string	$orderBy
	 * @param string	$orderType
	 * 
	 * @return object Sales info
	 */
	public function getPagedSales(
							 $withData = true,
							 $byStatus = 'S',
							 $byUser = '',
							 $orderBy = 'C',
							 $orderType = 'A',
                             $pageNumber = 1,
							 $pageSize = 20){
		//$withData = $this->booleanConvert($withData);
		return $this->clientConnect('individual','Sale','getPagedSales',get_defined_vars());
	}
	
	//End Sale Service
		
	
	
	//Start Messages Service
	
	/**
	 * Gelen kutusundaki mesaj bilgilerine erismek icin kullanilmasi gereken metoddur.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getInboxMessages-UserMessageService-soap-individual 
	 * 
	 * UserMessage Service
	 * Get Inbox Message Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * @param string	$unread
	 * 
	 * @return object Inbox Message List
	 */
	public function getInboxMessages($startOffset = 0 ,$rowCount = 5, $unread = true){
		//$unread = $this->booleanConvert($unread);
		return $this->clientConnect('individual','UserMessage','getInboxMessages',get_defined_vars());
	}

	
	/**
	 * Gonderilen kutusundaki mesaj bilgilerine erismek icin kullanilmasi gereken metoddur.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getSendedMessages-UserMessageService-soap-individual
	 * 
	 * UserMessage Service
	 * Get Outbox Message Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * 
	 * @return object Outbox Message List
	 */
	public function getSendedMessages($startOffset = 0 ,$rowCount = 5){
		return $this->clientConnect('individual','UserMessage','getSendedMessages',get_defined_vars());
	}
	
	
	/**
	 * Mesaj gonderme islemi icin kullanilmasi gereken metoddur.
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/sendNewMessage-UserMessageService-soap-individual
	 * 
	 * UserMessage Service
	 * Send New Message Method
	 * 
	 * @param string $to
	 * @param string $title
	 * @param string $messageContent
	 * @param string $sendCopy
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function sendNewMessage($to = null,$title = null,$messageContent = null, $sendCopy = null){
		return $this->clientConnect('individual','UserMessage','sendNewMessage',get_defined_vars());
	}
	
	
	
	/**
	 * UserMessage Service
	 * readMessage Method
	 * 
	 * @param integer	$startOffset
	 * @param integer	$rowCount
	 * 
	 * @return object Outbox Message List
	 */
	public function readMessage($messageId){
		return $this->clientConnect('individual','UserMessage','readMessage',get_defined_vars());
	}
	
	
	
	/**
	 * UserMessage Service
	 * deleteIncomingMessages Method
	 * 
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function deleteIncomingMessages($messageIds = array()){
		$xml = '<messageId>';
		if (count($messageIds) > 0){
			foreach ($messageIds as $messageId){
				$xml .= "<item>{$messageId}</item>";
			}
		}
		$xml .= '</messageId>';
		$options['messageId'] = $xml;
		
		return $this->clientConnect('individual','UserMessage','deleteIncomingMessages',$options,array('messageId'));
	}
	
	
	
	/**
	 * UserMessage Service
	 * deleteOutgoingMessages Method
	 * 
	 * 
	 * @return Web Servis mesajini object olarak dondurur
	 */
	public function deleteOutgoingMessages($messageIds = array()){
		$xml = '<messageId>';
		if (count($messageIds) > 0){
			foreach ($messageIds as $messageId){
				$xml .= "<item>{$messageId}</item>";
			}
		}
		$xml .= '</messageId>';
		$options['messageId'] = $xml;
		
		return $this->clientConnect('individual','UserMessage','deleteOutgoingMessages',$options,array('messageId'));
	}
	
	
	//End Message Service
	
	
	
	//Cargo Service
	
	/**
	 * Bu metot, satış kodunu girdikten sonra mevcut satışın kargo bilgisini girmek için kullanılır. 
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getCargoInformation-SaleService-soap-individual
	 * 
	 * getCargoInformation Service
	 * Get Cargo Information
	 * 
	 * @param integer	$saleCode
	 * 
	 * @return object Cargo Information
	 */
	public function getCargoInformation($saleCode){
		return $this->clientConnect('individual','Cargo','getCargoInformation',get_defined_vars());
	}
	
	
	/**
	 * İlgili satış kodu girildikten sonra, satış bilgisine kargo bilgisi eklemek için kullanılır. Bu metot satıcılar tarafından kullanılır. 
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/sendCargoInformation-SaleService-soap-individual
	 * 
	 * sendCargoInformation Service
	 * Send Cargo Information
	 * 
	 * @param integer	$saleCode
	 * 
	 * @return object Cargo Information
	 */
	public function sendCargoInformation($saleCode,$cargoPostCode,$cargoCompany,$cargoBranch,$followUpUrl,$userType){
		return $this->clientConnect('individual','Cargo','sendCargoInformation',get_defined_vars());
	}
	
	
	//End Cargo Service
	
	
	
	//Store Service
	
	/**
	 * Kullanicinin dukkan ve dukkan kategori bilgilerine ulasmak icin kullanilir. 
	 * 
	 * Detayli Bilgi : http://dev.gittigidiyor.com/metotlar/getStore-StoreService-soap-individual
	 * 
	 * Store Service
	 * getStore  Information
	 * 
	 * @param integer	$saleCode
	 * 
	 * @return object Web Servis mesajini object olarak dondurur
	 */
	public function getStore(){
		return $this->clientConnect('individual','Store','getStore',get_defined_vars());
	}
	
	//End Store Service
	
	/**
	 * Client Boolean Value Converter
	 * 
	 * @param $data
	 * 
	 * @return string $data
	 */
	protected function booleanConvert($data){
		if ($data){
			$data = 'true';
		}else{
			$data = 'false';
		}
		return $data;
	}
	
	
	
	/**
	 * GG Client Curl Connect Service ON SOAP
	 * 
	 * @param string $serviceAccessType
	 * @param string $serviceType
	 * @param string $method
	 * @param array  $parameters
	 * 
	 */
	protected  function clientConnect($serviceAccessType,$serviceType,$method,$parameters,$xml = array()){

		$soapParams = array();
		if ($serviceType =='Product' || $serviceType =='Developer'){
			$url = $this->developer_base_url;
		}else{
			$url = $this->product_base_url;
		}
	   	$url .= 'listingapi/ws/';
	   	switch ($serviceAccessType) {
	   		case 'anonymous': 
	   			$url .= $serviceType.'Service';
	   			break;
	   		case 'individual':
	   			$url .= 'Individual'.$serviceType.'Service';
	   			$soapParams = array('apiKey' => $this->apiKey,
									'sign' => $this->sign,
									'time' => (float)$this->time);

	   			break;		
	   		case 'internal':
	   			$url .= 'Internal'.$serviceType.'Service';break;
	   			
	   		case 'community':
	   			$url .= 'Community'.$serviceType.'Service';break;
	   	}
	   	$url .= '?wsdl';
	   	
		foreach ($parameters as $key => $param){
            $soapParams[$key]=$param;

	   	}
	   	if (count($xml) > 0){
	   		foreach ($xml as $xmlRow){
	   			$value = $parameters[$xmlRow];
				$soapParams[$xmlRow] = new SoapVar($value,XSD_ANYXML);		
	   		}
	   	}
	   	$soapParams['lang'] = $this->lang;

		$soapClient = new \SoapClient($url, array('login' => $this->auth_user, 'password' => $this->auth_pass, 'authentication' => SOAP_AUTHENTICATION_BASIC));
		$result = $soapClient->__soapCall($method,$soapParams);
		
		return $result;
	}	
	
}