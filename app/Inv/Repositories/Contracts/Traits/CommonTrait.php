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

    function filterRiskCommentPositive($item) 
    { 
      return $item['deal_type']==1;
    }

    function filterRiskCommentNegative($item) 
    { 
      return $item['deal_type']==2;
    }
}