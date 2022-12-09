<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;

return new class extends Migration
{
    static function dbTableName(): string
    {
        return (new Booking())->getTable();
    }

    public function up()
    {
        Schema::create(self::dbTableName(), function (Blueprint $table) {
            $table->id();
            $table->date('date_start');
            $table->date('date_end');
            $table->string('email');
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::dbTableName());
    }
};
