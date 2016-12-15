<?php

namespace salyangoz\pazaryeriparasut;

use Parasut\Client as ParasutClient;

class ParasutAdapter
{
    protected $parasut;
    protected $localStorage;
    protected $invoice  =   [];
    protected $marketplace;


    public function __construct(array $config, $marketplace)
    {
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

        $this->localStorage =   new LocalStorage();

        $this->marketplace = $marketplace;

    }

    /**
     * Yeni müşteri kaydı oluşturur
     * @param $customerDetails
     * @return mixed
     */
    private function createContact($customerDetails)
    {
        return $this->parasut->make('contact')->create(
            $customerDetails
        );
    }

    /**
     * Paraşütte yeni ürün kaydı oluşturur
     * @param $productData
     * @return mixed
     */
    private function createProduct($productData)
    {
        return $this->parasut->make('product')->create(
            $productData
        );
    }

    /**
     * Faturaya ürün ekler
     * @param $productName
     * @param $productID
     * @return bool|mixed
     */
    private function setProduct($productName,$productID)
    {
        if(!$parasutProductID = $this->localStorage->get("product.{$this->marketplace}_".$productID))
        {
            $product = $this->createProduct(["name"=>$productName,"code"=>"{$this->marketplace}_".$productID]);

            $parasutProductID = $product['product']['id'];
            $this->localStorage->set('product',"{$this->marketplace}_{$productID}",$parasutProductID);
            $this->localStorage->save();
        }

        return $parasutProductID;
    }

    /**
     * Müşteri oluşturur ve faturaya ekler
     * @param $buyerID
     * @param $fullName
     * @param $address
     * @param $taxID
     * @param $taxOffice
     * @param $city
     * @param $district
     * @param $phone
     * @param $email
     * @return bool|mixed
     */
    public function setContact($contactType,$buyerID,$fullName,$address,$taxID,$taxOffice,$city,$district,$phone,$email)
    {

        $parasutContactID = $this->localStorage->get("customer.{$this->marketplace}_".$buyerID);

        if(!$parasutContactID){

            $parasutCustomer    =   [
                'contact_type'  =>  $contactType,
                'name' => $fullName,
                'email' => $email,
                'tax_number' => $taxID,
                'tax_office' => $taxOffice,
                'category_id' => null,
                'address_attributes' => [
                    'address' => $address,
                    'phone' => $phone,
                    'fax' => null,
                ],
                'city'     =>$city,
                'district' =>$district
            ];

            $contact            =   $this->createContact($parasutCustomer);
            $parasutContactID   =   $contact['contact']['id'];

            $this->localStorage->set('customer',"{$this->marketplace}_".$buyerID,$parasutContactID);
            $this->localStorage->save();
        }

        $this->invoice['contact_id']    =   $parasutContactID;

        return $parasutContactID;
    }

    /**
     * Ürün oluşturur ve faturaya ekler
     * @param $productName
     * @param $productID
     * @param $quantity
     * @param $amount
     */
    public function addProduct($productName,$productID,$quantity,$amount)
    {

        $parasutProductID   =   $this->setProduct($productName,$productID);

        $this->invoice['details_attributes'][] =
            [
                'product_id'        => $parasutProductID,
                'quantity'          => $quantity,
                'discount_value'    => 0,
                'discount_type'     => 'amount',
                'unit_price'        => ($amount) / (1 + 0.18),
                'vat_rate'          => 0.18 * 100,
            ];

    }

    /**
     * Bilgileri girilen faturayı kayıt eder
     * @param $saleID
     * @param $total
     * @param $invoiceDescription
     * @param $invoiceDate
     */
    public function saveInvoice($saleID,$total,$invoiceDescription,$invoiceDate)
    {

        $this->invoice    =   array_merge($this->invoice,[
            'item_type'     => 'invoice',
            'description'   => $invoiceDescription,
            'issue_date'    => $invoiceDate,
            'invoice_series'=> $this->marketplace,
            'category_id'   => config("pazaryeri-parasut.parasut_category_id"),
            'payment_status'=>'paid',
        ]);

        /**
         * Sipariş daha önce işlendiyse atlıyor
         */
        if($this->localStorage->get("order.{$this->marketplace}_{$saleID}"))
            return;

        /**
         * Fatura kaydı oluşturuluyor
         */
        $response = $this->parasut->make('sale')->create($this->invoice);

        /**
         * Fatura ödendi yapılıyor ve ödeme belirlenen hesap kasasına kaydediliyor
         */
        $this->parasut->make('sale')->paid(
            $response['sales_invoice']['id'],
            [   "amount"=>$total,
                "date"=>date('Y-m-d'),
                "account_id"=>Config('pazaryeri-parasut.parasut_account_id')]
        );

        $this->localStorage->set('order',"{$this->marketplace}_".$saleID,$response['sales_invoice']['id']);
        $this->localStorage->save();
    }
}