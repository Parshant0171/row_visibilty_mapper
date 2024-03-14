<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceToRvmConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rvm_condition_fields', function (Blueprint $table) {
            $table->tinyInteger('sequence')->after('field_fetch_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rvm_condition_fields', function (Blueprint $table) {
            $table->dropColumn('sequence');
        });
    }
}
