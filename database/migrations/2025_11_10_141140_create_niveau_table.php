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
        Schema::create('niveau', function (Blueprint $table) {
           $table->increments("code_niveau");
            $table->string("label_niveau", 256);
            $table->text("desc_niveau");
            $table->string("code_filiere",20);
            $table->foreign("code_filiere")->references("code_filiere")->on("filiere");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveau');
    }
};
