<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->string('filename', 155);
            $table->string('hash', 255);
            $table->integer('status')->default(0);
            $table->enum('location', ['local', 's3']);
            $table->string('path', 255)->nullable();
            $table->string('publisher_email', 255)->nullable()->default(null);
            $table->string('publisher_name', 255)->nullable()->default(null);
            $table->timestamp('started_processing')->nullable()->default(null);
            $table->timestamp('finished_processing')->nullable()->default(null);
            $table->string('request_ip', 50)->nullable()->default(null);
            $table->string('parser_class', 155)->nullable()->default(null);
            $table->string('parser_version', 50)->nullable()->default(null);
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
