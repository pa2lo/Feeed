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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
			$table->boolean('active')->default(false);
			$table->string('name');
			$table->string('url');
			$table->string('network');
			$table->string('network_id')->nullable();
			$table->string('thumbnail')->nullable();
			$table->string('status')->nullable();
			$table->time('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
