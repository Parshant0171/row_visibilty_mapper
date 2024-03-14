<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRvmConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rvm_conditions', function (Blueprint $table) {
            $table->id();

            if(config('row-visibility.mapper.useTenants') == 1){
                $table->foreignId('tenant_id');            
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            }

            $table->morphs('visibility_mappable', 'rvm_map_poly_id');

            $table->unsignedBigInteger('rvm_condition_field_id');
            $table->foreign('rvm_condition_field_id', 'rvm_con_f_id')->references('id')->on('rvm_condition_fields')->onDelete('cascade');

            $table->string('rvm_value');
            
            $table->auditableWithDeletes();

            $table->timestampTz('created_at', $precision = 0)->useCurrent();
            $table->timestampTz('updated_at', $precision = 0)->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rvm_conditions');
    }
}
