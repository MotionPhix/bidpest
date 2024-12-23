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
    Schema::create('bids', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->foreignId('tender_document_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->enum('status', ['draft', 'in_review', 'submitted', 'accepted', 'rejected'])->default('draft');
      $table->json('compliance_score')->nullable();
      $table->decimal('total_price', 15, 2)->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('bids');
  }
};
