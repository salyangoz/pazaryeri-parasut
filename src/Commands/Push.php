<?php

namespace salyangoz\pazaryeriparasut\Commands;

use salyangoz\pazaryeriparasut\PazaryeriParasut;

use Illuminate\Console\Command;

class Push extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pazaryeriparasut:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save orders to parasut from your heart';

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

        App(PazaryeriParasut::class)->push();

    }
}
