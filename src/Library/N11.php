<?php

namespace Salyangoz\pazaryeriparasut\Library;

Class N11 {

    protected static $_appKey, $_appSecret, $_parameters, $_sclient, $_baseUrl;
    public $_debug = false;
    
    public function __construct(array $attributes = array()) {
        self::$_appKey = $attributes['appKey'];
        self::$_appSecret = $attributes['appSecret'];
        self::$_baseUrl =   $attributes['baseUrl'];
        self::$_parameters = ['auth' => ['appKey' => self::$_appKey, 'appSecret' => self::$_appSecret]];
    }
    
    public function setUrl($url) {
        self::$_sclient = new \SoapClient(self::$_baseUrl.$url);
    }

    public function GetTopLevelCategories() {
        $this->setUrl('CategoryService.wsdl');
        return self::$_sclient->GetTopLevelCategories(self::$_parameters);
    }

    public function GetCities() {
        $this->setUrl('CityService.wsdl');
        return self::$_sclient->GetCities(self::$_parameters);
    }

    public function GetProductList($itemsPerPage, $currentPage) {
        $this->setUrl('ProductService.wsdl');
        self::$_parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
        return self::$_sclient->GetProductList(self::$_parameters);
    }

    public function GetProductBySellerCode($sellerCode) {
        $this->setUrl('ProductService.wsdl');
        self::$_parameters['sellerCode'] = $sellerCode;
        return self::$_sclient->GetProductBySellerCode(self::$_parameters);
    }

    public function SaveProduct(array $product = Array()) {
        $this->setUrl('ProductService.wsdl');
        self::$_parameters['product'] = $product;
        return self::$_sclient->SaveProduct(self::$_parameters);
    }

    public function DeleteProductBySellerCode($sellerCode) {
        $this->setUrl('ProductService.wsdl');
        self::$_parameters['productSellerCode'] = $sellerCode;
        return self::$_sclient->DeleteProductBySellerCode(self::$_parameters);
    }
    
    public function DetailedOrderList(array $searchData = Array(),array $pagingData = Array()) {
        $this->setUrl('OrderService.wsdl');
        self::$_parameters['searchData'] = $searchData;
        self::$_parameters['pagingData'] = $pagingData;
        return self::$_sclient->DetailedOrderList(self::$_parameters);
    }
    
    public function OrderDetail(array $orderRequest = Array()) {
        $this->setUrl('OrderService.wsdl');
        self::$_parameters['orderRequest'] = $orderRequest;
        return self::$_sclient->OrderDetail(self::$_parameters);
    }

    public function __destruct() {
        if ($this->_debug) {
            print_r(self::$_parameters);
        }
    }

    public function checkResponse($response)
    {
        if($response->result->status == "failure")
        {
            throw new \Exception($response->result->errorMessage . " " . $response->result->errorCode);
            exit();
        }

        return true;
    }
    
}