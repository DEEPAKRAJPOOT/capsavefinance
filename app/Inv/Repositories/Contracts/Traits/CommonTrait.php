<?php
namespace App\Inv\Repositories\Contracts\Traits;

trait CommonTrait
{
    function filterPreCond($item) 
    { 
      return $item['cond_type']==1;
    }

    function filterPostCond($item) 
    { 
      return $item['cond_type']==2;
    }
}