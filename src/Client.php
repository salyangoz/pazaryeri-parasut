<?php

namespace salyangoz\pazaryeriparasut;
use Carbon\Carbon;
use salyangoz\pazaryeriparasut\Marketplace\Hepsiburada;
use salyangoz\pazaryeriparasut\Services\EInvoice;

class Client implements PazaryeriParasut
{

    private $config;

    /**
     * Constructor.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config   =   $config;
    }

    public function transfer()
    {
        $einvoice   =   new EInvoice($this->config);
        $einvoice->transfer();
    }

    public function transferEInvoices()
    {
        $parasutAdapter =   new ParasutAdapter($this->config,"GG");
        $parasutAdapter->transferEInvoices();
    }

    public function pull()
    {
        //Todo: Tüm clienlar açılacak

        $gittigidiyorMarket = new Marketplace\Gittigidiyor($this->config);
        $gittigidiyorMarket->pull();

        $n11    =   new Marketplace\N11($this->config);
        $n11->pull();

        $hepsiburada    =   new Hepsiburada($this->config);
        $hepsiburada->pull();
    }

    public function push()
    {
        $push = new Push($this->config);
        $push->start();
    }

    public function einvoiceRequest()
    {
        $einvoice   =   new EInvoice($this->config);
        $einvoice->request();
    }

}
