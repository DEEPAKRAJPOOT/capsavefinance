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
    protected $signature = 'lms:eodprocess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EOD Process';

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
        \App::make('App\Http\Controllers\Lms\EodProcessController')->process();
    }
}
