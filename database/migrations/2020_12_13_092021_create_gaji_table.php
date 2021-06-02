<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGajiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->id();
            $table->Integer('pegawai_id')->nullable();
            $table->text('komponen_gaji')->nullable();
            $table->string('nominal_gaji')->nullable();
            $table->text('komponen_potongan')->nullable();
            $table->string('nominal_potongan')->nullable();
            $table->string('total_gaji')->nullable();
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
        Schema::dropIfExists('gaji');
    }
}
