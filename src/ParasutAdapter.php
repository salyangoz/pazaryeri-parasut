<?php

namespace salyangoz\pazaryeriparasut;

use Parasut\Client as ParasutClient;
use Illuminate\Support\Facades\App;
use Mail;

class ParasutAdapter
{
    public $parasut;
    protected $localStorage;
    protected $invoice  =   [];
    protected $eInvoiceDefaults;
    protected $paymentPlatforms;


    public function __construct(array $config)
    {
        // create a new client instance
        $this->parasut = new ParasutClient([
            'client_id'     => array_get($config, 'parasut_client_id'),
            'client_secret' => array_get($config, 'parasut_client_secret'),
            'username'      => array_get($config, 'parasut_username'),
            'password'      => array_get($config, 'parasut_password'),
            'company_id'    => array_get($config, 'parasut_company_id'),
            'grant_type'    => 'password',
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob',
        ]);

        $this->parasut->authorize();

        $this->localStorage =   new LocalStorage();

        $paymentPlatforms   =   [
            "HB"    =>  "Hepsiburada",
            "GG"    =>  "Gittigidiyor",
            "N11"   =>  "N11"
        ];

        $this->paymentPlatforms =   $paymentPlatforms;

        $this->eInvoiceDefaults =   [
            'internet_sale'         =>[
                "payment_type"      => config("pazaryeri-parasut.einvoice_payment_type"),
            ],
            'vat_withholding_code'  =>config("pazaryeri-parasut.einvoice_vat_withholding_code")
        ];

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
            $product = $this->createProduct(["name"=>$productName, "code"=>"{$this->marketplace}_".$productID]);

            $parasutProductID = $product['product']['id'];
            $this->localStorage->set('product',"{$this->marketplace}_{$productID}", $parasutProductID);
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
    public function saveInvoice($saleID,$total,$invoiceDescription,$invoiceDate,$taxNumber)
    {

        $this->invoice      =  array_merge($this->invoice,[
            'item_type'     => 'invoice',
            'description'   => $invoiceDescription,
            'issue_date'    => $invoiceDate,
            'invoice_series'=> $this->marketplace,
            'category_id'   => config("pazaryeri-parasut.parasut_category_id"),
            'payment_status'=> 'paid',
        ]);

        /**
         * Sipariş daha önce işlendiyse atlıyor
         */
        if($this->localStorage->get("order.{$this->marketplace}_{$saleID}"))
            return;

        try{

            /**
             * Fatura kaydı oluşturuluyor
             */
            $response = $this->parasut->make('sale')->create($this->invoice);

            /**
             * Fatura ödendi yapılıyor ve ödeme belirlenen hesap kasasına kaydediliyor
             */
            $this->parasut->make('sale')->paid(
                $response['sales_invoice']['id'],
                [   "amount"    =>  $total,
                    "date"      =>  date('Y-m-d'),
                    "account_id"=>  Config('pazaryeri-parasut.parasut_account_id')]
            );

            $this->localStorage->set('order',"{$this->marketplace}_".$saleID,$response['sales_invoice']['id']);
			$this->localStorage->save();

            $this->makeEInvoice("{$this->marketplace}_".$saleID, $response['sales_invoice']['id'], $taxNumber);


        }catch (\Exception $e)
        {
            echo $e->getMessage()."\n";
        }

    }

    private function makeEInvoice($saleID,$invoiceID,$taxNumber)
    {
		$this->localStorage->set('waiting_einvoice',$saleID,$invoiceID);
		$this->localStorage->save();
			
        /** İstek yapmadan önce daha önce istek yapılmış mı kontrol ediliyor */
        $eInvoiceStatus	=	$this->parasut->make('sale')->getEInvoiceStatus($invoiceID);


        if($eInvoiceStatus['status']!="done")
        {

            $eInvoiceTypeData	=	$this->parasut->make('sale')->getEInvoiceType($invoiceID);

            if($eInvoiceTypeData['e_document_type'] == 'e_archive' )
            {

                $this->eArchiveRequest($invoiceID);

            }
            elseif($eInvoiceTypeData['e_document_type'] == 'e_invoice' )
            {

                $this->eInvoiceRequest($invoiceID,$taxNumber);
            }
        }
        else
        {
            $this->localStorage->delete('waiting_einvoice',$saleID);
            $this->localStorage->save();
        }
    }

    private function eArchiveRequest($invoiceID)
    {
        $eArchiveResponse = $this->parasut->make('sale')->createEArchive($invoiceID,
            ["internet_sale"=>array_merge(
                $this->eInvoiceDefaults,["internet_sale"=>['payment_date'=>strtotime("now")]]
            )]);

        if(isset($eArchiveResponse['success'])) {

            if ($eArchiveResponse['success'] == 'OK') {

                return true;
            }
        }

        return false;
    }

    /** E-fatura isteği yapar
     * @param $saleToken
     * @param $invoice
     * @param $tax
     */
    private function eInvoiceRequest($invoiceID,$tax)
    {
        //Get customer inboxes
        $eInvoiceInboxes =  $this->parasut->make('sale')->getEInvoiceInboxes($tax);
        if($eInvoiceInboxes['e_invoice_inboxes'] !== null)
        {

            $inboxes        =   $eInvoiceInboxes['e_invoice_inboxes'];
            $inboxAddress	=   $inboxes[0]['e_invoice_address'];

            try{

                $eInvoiceResponse =  $this->parasut->make('sale')->createEInvoice($invoiceID,
                    array_merge(
                        [
                            'scenario'=>'commercial',
                            'to'=>$inboxAddress
                        ],
                        $this->eInvoiceDefaults
                    ));

                if($eInvoiceResponse['success'] !== null)
                {

                    if($eInvoiceResponse['success'] == 'OK')
                    {
                        return true;
                    }
                }

            }catch(Exception $e)
            {
                Log::error("Fatura isteği hatası (eInvoice) ({Fatura: $invoiceID}):".$e->getMessage());
            }
        }

    }

    /**
     * S3'e aktarılmamış veya emaili gönderilmemiş e-faturaları s3'e aktarır ve emailini iletir
     */
    public function transferEInvoices()
    {

        //E faturası bekleyen faturalar
        $eInvoiceRequestedInvoices = (array)$this->localStorage->get('waiting_einvoice');

        foreach ($eInvoiceRequestedInvoices as $orderID=>$invoice)
        {

            $eInvoiceStatus	=	$this->parasut->make('sale')->getEInvoiceStatus($invoice);
            $saleInvoice 	=	$this->parasut->make('sale')->find($invoice);

            if(isset($saleInvoice['error']))
            {
                continue;
            }

            if($eInvoiceStatus['status'] == "done" && $saleInvoice['sales_invoice']['item_type'] == 'invoice')
            {
                $saleInvoice = $this->parasut->make('sale')->find($invoice);
                $customer    = $saleInvoice['sales_invoice']['contact'];

                $eInvoicePath   =   $this->replaceTr(date('Y-m-d')."/{$customer['name']}-{$invoice}.pdf");

                $s3Transfer =   $this->s3Transfer($eInvoicePath, $eInvoiceStatus['pdf']['url']);

                if($s3Transfer)
                {

                    list($marketplace,$orderNumber)  =   explode('_',$orderID);
                    $marketplaceProvider =  $this->paymentPlatforms[$marketplace];

                    $emailSent = $this->sendEInvoiceMail($customer['name'], $customer['email'],
                                config('pazaryeri-parasut.aws.invoice_bucket_url').$eInvoicePath, $orderNumber,
                                "$marketplaceProvider"." ".config('pazaryeri-parasut.marketplace.name'));

                    /**
                     * Email de başarıyla gönderildiyse fatura e-faturası bekleyenler arasından siliniyor.
                     */
                    if($emailSent)
                    {
                        $this->localStorage->delete('waiting_einvoice',$orderID);
			$this->localStorage->save();
                    }
                }

            }
        }
    }


    /**
     * Metindeki türkçeye has karekterleri ingilizce alternatifleriyle değiştirir.
     * @param $text
     * @return string
     */
    public static function replaceTr($text) {
        $text = trim($text);
        $search = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ', 'â', 'Â');
        $replace = array('c','c','g','g','i','i','o','o','s','s','u','u','_','a','a,');
        $new_text = str_replace($search,$replace,$text);
        return strtolower($new_text);
    }
}
