<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LenovoNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:lenovoNewUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for Lenovo new user and send notifications';

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
        
        \App::make('App\Http\Controllers\Auth\LenevoRegisterController')->checkLenevoNewUser();
            
    }
}
