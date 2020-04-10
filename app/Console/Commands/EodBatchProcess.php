<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EodBatchProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:eodbatchprocess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EOD Batch Process';

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
        \App::make('App\Http\Controllers\Lms\EodBatchProcessController')->process();
    }
}
