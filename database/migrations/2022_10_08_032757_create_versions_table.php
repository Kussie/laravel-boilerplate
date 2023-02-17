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
        Schema::create('versions', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->string('package_hash', 255);
            $table->string('type', 100)->index();
            $table->integer('version')->index();
            $table->string('slug', 255)->index();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->text('summary')->nullable()->default(null);
            $table->boolean('parsed')->default(0);
            $table->boolean('approved')->default(0);
            $table->boolean('visible')->default(0);
            $table->boolean('archived')->default(0);
            $table->json('files')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
