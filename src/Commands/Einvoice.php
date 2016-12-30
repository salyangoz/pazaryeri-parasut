<?php

namespace salyangoz\pazaryeriparasut\Commands;

use salyangoz\pazaryeriparasut\PazaryeriParasut;

use Illuminate\Console\Command;

class Einvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pazaryeriparasut:einvoice {action : request or transfer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make einvoice request for waiting orders or transfer ready orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $action = $this->argument('action');

        if($action == 'request')
        {
            App(PazaryeriParasut::class)->einvoiceRequest();
        }
        elseif($action == 'transfer')
        {
            App(PazaryeriParasut::class)->transfer();
        }

    }
}
