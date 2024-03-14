<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRvmConditionFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rvm_condition_fields', function (Blueprint $table) {
            $table->id();

            if(config('row-visibility.mapper.useTenants') == 1){
                $table->foreignId('tenant_id');            
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            }

            $table->unsignedBigInteger('rvm_master_id');
            $table->foreign('rvm_master_id')->references('id')->on('rvm_masters')->onDelete('cascade');

            $table->string('field_name');            
            
            $table->tinyInteger('uses_relationship')->default(0);

            $table->json('field_fetch_method')->nullable();

            $table->json('display_options')->nullable();

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
        Schema::dropIfExists('rvm_condition_fields');
    }
}
