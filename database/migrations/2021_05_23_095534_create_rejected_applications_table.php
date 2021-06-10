<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectedApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejected_applications', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('account_type');
            $table->string('company_name');
            $table->string('company_registration_number');
            $table->string('company_owner_name');
            $table->string('company_email');
            $table->string('company_phone');
            $table->string('company_website')->nullable();
            $table->string('company_address');
            $table->string('company_city');
            $table->string('company_country');
            $table->string('reason');
            $table->dateTime('rejected_at');
            $table->dateTime('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejected_applications');
    }
}
