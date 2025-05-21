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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');//e.g., "Size", "Color", "Country of Origin"
            $table->string('slug')->unique();
            $table->string('type')->default('text');//['text', 'number', 'select', 'checkbox', 'radio']
            $table->boolean('is_visible')->default(true);//show on product page
            $table->unsignedTinyInteger('sort_order')->default(0);  
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
