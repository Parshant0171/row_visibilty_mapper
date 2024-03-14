<?php

namespace Jgu\RowVisibilityMapper\Models;

class RvmCondition extends BaseRvmModel {

    public function rvmFields(){
        return $this->belongsTo(RvmConditionField::class, 'rvm_condition_field_id');
    }

    public function visibilityMappable(){
        return $this->morphTo();
    }
}