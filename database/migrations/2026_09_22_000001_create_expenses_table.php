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
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_code', 50)->unique();
                $table->date('date')->index();
                $table->string('category', 100)->index();
                $table->string('subcategory', 100)->index();
                $table->string('purpose', 255);
                $table->decimal('amount', 15, 2);
                $table->text('notes')->nullable();
                $table->string('receipt_photo', 255)->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
