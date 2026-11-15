<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_report_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_report_id')->constrained()->cascadeOnDelete();
            $table->string('path'); // path relatif pada disk "public"
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['device_report_id', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_report_images');
    }
};
