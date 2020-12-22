<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiClientTables extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_key')->unique();
            $table->string('name')->unique();
            $table->string('webhook_url');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('api_client_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('api_client_api_client_scope', function (Blueprint $table) {
            $table->unsignedInteger('api_client_id')->nullable();
            $table->foreign('api_client_id')->references('id')->on('api_clients');

            $table->unsignedInteger('api_client_scope_id')->nullable();
            $table->foreign('api_client_scope_id')->references('id')->on('api_client_scopes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_client_scopes');
        Schema::dropIfExists('api_client_scopes');
        Schema::dropIfExists('api_clients');
    }
}
