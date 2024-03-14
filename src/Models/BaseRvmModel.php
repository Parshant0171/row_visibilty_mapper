<?php

namespace Jgu\RowVisibilityMapper\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Yajra\Auditable\AuditableWithDeletesTrait;

if(config('row-visibility-mapper.useTenants') && trait_exists('\App\Traits\ExTrait')){
    class BaseRvmModel extends Model
    {
        use HasFactory;
        use AuditableWithDeletesTrait, SoftDeletes;
        use \App\Traits\ExTrait;     

    }
}else{
    class BaseRvmModel extends Model
    {
        use HasFactory;
        use AuditableWithDeletesTrait, SoftDeletes;

    }
}