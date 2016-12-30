<?php

namespace salyangoz\pazaryeriparasut\Commands;

use salyangoz\pazaryeriparasut\PazaryeriParasut;

use Illuminate\Console\Command;

class Pull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pazaryeriparasut:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save orders to db from n11,hepsiburada,gittigidiyor';

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

        App(PazaryeriParasut::class)->pull();

    }
}
