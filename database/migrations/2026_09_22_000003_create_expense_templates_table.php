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
        if (!Schema::hasTable('expense_templates')) {
            Schema::create('expense_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('icon', 50)->default('zap');
                $table->string('category', 100);
                $table->string('subcategory', 100);
                $table->string('purpose', 255);
                $table->decimal('amount', 15, 2);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order_num')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_templates');
    }
};
