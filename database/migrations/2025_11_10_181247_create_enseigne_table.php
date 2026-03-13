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
        Schema::create('enseigne', function (Blueprint $table) {
            $table->string("code_pers", 20);
            $table->string("code_ec", 20);
            $table->foreign("code_pers")->references("code_pers")->on("personnel");
            $table->foreign("code_ec")->references("code_ec")->on("ec");
            $table->primary(["code_ec", "code_pers"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseigne');
    }
};
