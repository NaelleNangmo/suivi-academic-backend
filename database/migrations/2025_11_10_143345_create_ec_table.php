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
        Schema::create('ec', function (Blueprint $table) {
            $table->string("code_ec",20)->primary();
            $table->string("label_ec");
            $table->text("desc_ec");
            $table->integer("nbh_ec");
            $table->integer("nbc_ec");
            $table->string("support_cours")->nullable();
            $table->string("code_ue", 20);
            $table->foreign("code_ue")->references("code_ue")->on("ue");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec');
    }
};
