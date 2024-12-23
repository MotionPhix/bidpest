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
    Schema::create('tender_documents', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('client_name');
      $table->date('submission_deadline');
      $table->json('requirements')->nullable();
      $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
      $table->foreignId('user_id')->constrained();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tender_documents');
  }
};
