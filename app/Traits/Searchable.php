<?php

namespace App\Traits;

trait SearchAble {
    
    public function scopeSearchable($query, ...$colums)
    {
        $keyWord = request()->search; 
        if ($keyWord) {
            foreach ($colums as $k => $colum) {
                $query->orWhere($colum, 'like', "%$keyWord%");
            }
        }
    }
    public function scopeSearchable1($query, ...$colums)
    {
        $keyWord = request()->search; 
        if ($keyWord) {
            foreach ($colums as $k => $colum) {
                $query->orWhere($colum, '=', "$keyWord");
            }
        }
    }
}
