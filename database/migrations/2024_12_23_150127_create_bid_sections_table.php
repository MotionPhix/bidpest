<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('bid_sections', function (Blueprint $table) {
      $table->id();
      $table->foreignId('bid_id')->constrained();
      $table->string('title');
      $table->text('content')->nullable();
      $table->json('metadata')->nullable();
      $table->integer('order')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('bid_sections');
  }
};
