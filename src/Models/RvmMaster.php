<?php

namespace Jgu\RowVisibilityMapper\Models;

class RvmMaster extends BaseRvmModel {

    protected $with = ['rvmFields'];
    
    public function rvmFields(){
        return $this->hasMany(RvmConditionField::class);
    }

}