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
        Schema::create('device_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // client-generated, dipakai untuk idempotency (offline mobile)
            $table->foreignId('device_id')->constrained()->restrictOnDelete(); // FK numerik ke devices.id
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // pelapor
            $table->foreignId('gedung_id')->nullable()->constrained()->nullOnDelete(); // gedung hasil pengamatan (bukan Location)
            $table->dateTime('captured_at'); // waktu kejadian menurut klien
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'captured_at']);
            $table->index(['user_id', 'captured_at']);
            $table->index('captured_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_reports');
    }
};
