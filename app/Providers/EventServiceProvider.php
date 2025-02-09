<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Inv\Repositories\Observers\TransactionsObserver;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Observers\ChargeTransactionObserver;
use App\Inv\Repositories\Models\Lms\ChargesTransactions;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

     /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Inv\Repositories\Events\UserEventsListener',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Transactions::observe(TransactionsObserver::class);
        ChargesTransactions::observe(ChargeTransactionObserver::class);
    }
}
