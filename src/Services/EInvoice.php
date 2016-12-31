<?php

namespace salyangoz\pazaryeriparasut\Services;

use Carbon\Carbon;
use salyangoz\pazaryeriparasut\Models\Order;
use salyangoz\pazaryeriparasut\ParasutAdapter;
use Exception;
use Illuminate\Support\Facades\Log;
use App;
use Mail;

class EInvoice extends ParasutAdapter
{

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function request()
    {
        $orders =   Order::waitingEinvoice()->get();

        foreach ($orders as $order)
        {
            $this->makeEInvoice($order);
            usleep(300000);
        }
    }

    private function makeEinvoice(Order $order)
    {

        $eInvoiceStatus	=	$this->parasut->make('sale')->getEInvoiceStatus($order->parasut_id);

        if($eInvoiceStatus['status']!="done")
        {

            $eInvoiceTypeData	=	$this->parasut->make('sale')->getEInvoiceType($order->parasut_id);

            if($eInvoiceTypeData['e_document_type'] == 'e_archive' )
            {
                $this->makeEArchiveRequest($order);
            }
            elseif($eInvoiceTypeData['e_document_type'] == 'e_invoice' )
            {
                $this->makeEInvoiceRequest($order);
            }

        }
        else
        {
            $order->e_invoice_status = "request_sent";
            $order->save();
        }
    }

    private function makeEArchiveRequest(Order $order)
    {
		$eArchiveResponse  = [];
		
		try
		{
			$eArchiveResponse = $this->parasut->make('sale')->createEArchive($order->parasut_id,
            [
                "internet_sale"=>[
					"url"				=> "http://{$order->marketplace}.com",
					"payment_type"      => config("pazaryeri-parasut.einvoice_payment_type"),
                    "payment_platform"  => $order->marketplace,
                    "payment_date"      => date('Y-m-d'),
                ],
				'vat_withholding_code'  =>config("pazaryeri-parasut.einvoice_vat_withholding_code")]
			);
			
			if(isset($eArchiveResponse['success'])) {

				if ($eArchiveResponse['success'] == 'OK')
				{
					$order->e_invoice_status 		= "request_sent";
					$order->e_invoice_document_type = "e_archive";
					$order->save();

					return true;
				}
			}
			
		}catch(Exception $e)
		{
			Log::error("E-Arşiv işleme hatası: Sipariş id: {$order->id} ".$e->getMessage());
		}
		

        return false;

    }

    private function makeEInvoiceRequest(Order $order)
    {

        $eInvoiceInboxes =  $this->parasut->make('sale')->getEInvoiceInboxes($order->customer->tax_number);
        if($eInvoiceInboxes['e_invoice_inboxes'] !== null)
        {

            $inboxes        =   $eInvoiceInboxes['e_invoice_inboxes'];
            $inboxAddress	=   $inboxes[0]['e_invoice_address'];

            try{

                $eInvoiceResponse =  $this->parasut->make('sale')->createEInvoice($order->parasut_id,
                    array_merge([
                            'scenario'=>'commercial',
                            'to'=>$inboxAddress
                        ],
                        $this->eInvoiceDefaults
                    ));

                if($eInvoiceResponse['success'] !== null)
                {

                    if($eInvoiceResponse['success'] == 'OK')
                    {

                        $order->e_invoice_status = "request_sent";
                        $order->e_invoice_document_type  = "e_invoice";
                        $order->save();

                        return true;
                    }
                }

            }
            catch(Exception $e)
            {
                Log::error("Fatura isteği hatası (eInvoice) {Fatura: {$order->id}):".$e->getMessage());
            }
        }
    }

    public function transfer()
    {

        $orders = Order::avibleEinvoices()->get();

        foreach ($orders as $order)
        {

            $eInvoiceStatus	=	$this->parasut->make('sale')->getEInvoiceStatus($order->parasut_id);
            $saleInvoice 	=	$this->parasut->make('sale')->find($order->parasut_id);

            if(isset($saleInvoice['error']))
            {
                continue;
            }

            if($eInvoiceStatus['status'] == "done" && $saleInvoice['sales_invoice']['item_type'] == 'invoice')
            {

                $eInvoicePath   =   parent::replaceTr(date('Y-m-d')."/{$order->customer->name}-{$order->id}.pdf");

                $s3Transfer =   $this->s3Transfer($eInvoicePath, $eInvoiceStatus['pdf']['url']);

                if($s3Transfer)
                {

                    $order->e_invoice_url = config('pazaryeri-parasut.aws.invoice_bucket_url').$eInvoicePath;
                    $order->save();

                    $emailSent = $this->sendMail($order->customer->name, $order->customer->email,
                        config('pazaryeri-parasut.aws.invoice_bucket_url').$eInvoicePath, $order->order_id,
                        $order->marketplace. " " .config('pazaryeri-parasut.marketplace.name'));

                    /**
                     * Email de başarıyla gönderildiyse fatura e-faturası bekleyenler arasından siliniyor.
                     */
                    if($emailSent)
                    {
                        $order->e_invoice_status 		= "ready";
						$order->einvoice_created_at 	= Carbon::now();
                        $order->save();
                    }
                }
            }
			elseif($eInvoiceStatus['status'] == "error" && $saleInvoice['sales_invoice']['item_type'] == 'invoice')
			{
				$order->e_invoice_status 		= "waiting";
				$order->save();				
			}
        }
    }

    /**
     * Müşteriye faturasını email ile gönderir
     * @param $customerName
     * @param $customerEmail
     * @param $eInvoiceUrl
     * @param $orderID
     * @param $marketplaceName
     * @return bool
     */
    private function sendMail($customerName,$customerEmail,$eInvoiceUrl,$orderID,$marketplaceName)
    {
        $mailview   =   'pazaryeri-parasut::emails.einvoice';

        Mail::send($mailview, ['customerName' => $customerName,'orderID'=>$orderID, 'marketplaceName'=> $marketplaceName],

            function ($m) use ($eInvoiceUrl,$marketplaceName,$customerEmail)
            {
                $m->from(config('pazaryeri-parasut.mail.from_email'), config('pazaryeri-parasut.mail.from_name'));
                $m->to($customerEmail);

                $emails =   explode(",",config('pazaryeri-parasut.mail.cc_email'));

                foreach ($emails as $cc)
                {
                    $m->cc($cc);
                }

                $m->subject("{$marketplaceName} alışverişinizin e-faturası hazır!");
                $m->attach($eInvoiceUrl);
            });

        return true;
    }

    /**
     * Verilen urldeki dosyayı verilen s3 sunucusunda verilen pathe kopyalar.
     * @param $path
     * @param $pdfUrl
     * @return bool
     */
    private function s3Transfer($path,$pdfUrl)
    {
        $s3 = App::make('aws')->createClient('s3');

        $s3->putObject(array(
            'Bucket'     => config('pazaryeri-parasut.aws.invoice_bucket'),
            'Key'        => $path,
            'Body'       => file_get_contents($pdfUrl),
            'options'    => ['scheme'     => 'http']
        ));

        return true;
    }

}