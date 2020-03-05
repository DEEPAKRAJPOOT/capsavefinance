<?php

namespace App\Helpers;

use App\Inv\Repositories\Contracts\FinanceInterface;

class FinanceHelper {

    private $finRepo;
    public function __construct(FinanceInterface $finRepo) {
        $this->finRepo = $finRepo;
    }

    public static function test(){
        dd('kkkk');
    }
}