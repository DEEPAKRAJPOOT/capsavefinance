<?php

namespace App\Http\Middleware\Authorization;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class BaseAuthorization
{
    /**
     * Gate object
     *
     * @var string
     */
    protected $gate;
    
    /**
     * Constructor
     *
     * @param GateContract $gate
     */
    public function __construct(GateContract $gate)
    {
        $this->gate = $gate;
    }
}
