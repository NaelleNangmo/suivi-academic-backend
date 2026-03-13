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
        Schema::create('personnel', function (Blueprint $table) {
            $table->string("code_pers")->primary();
            $table->string("nom_pers");
            $table->string("prenom_pers")->nullable();
            $table->enum("sexe_pers", ["M", "F"]);
            $table->string("phone_pers");
            $table->string("login_pers");
            $table->string("pwd_pers");
            $table->enum("type_pers", ["ENSEIGNANT", "RESPONSABLE ACADEMIQUE", "RESPONSABLE DISCIPLINE"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel');
    }
};
