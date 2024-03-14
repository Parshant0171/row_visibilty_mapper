<?php

namespace Jgu\RowVisibilityMapper\Models;

use Illuminate\Database\Eloquent\Builder;

class RvmConditionField extends BaseRvmModel {

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('order_by_sequence', function (Builder $builder) {            
            $builder->orderBy('sequence');
        });
    }

    public function rvmMaster(){
        return $this->belongsTo(RvmMaster::class);
    }

    public function rvmConditions(){
        return $this->hasMany(RvmCondition::class);
    }

}