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
            $table->string('code_pers', 20)->primary();
            $table->string('nom_pers', 100);
            $table->string('prenom_pers', 100)->nulable();
            $table->enum('sexe_pers', ['M', 'F']);
            $table->string('phone_pers', 150);
            $table->string('login_pers', 150)->unique();
            $table->string('pdw_pers', 256);
            $table->enum('type_pers', ['ENSEIGNANT', 'RESPONSABLE ACADEMIQUE', 'RESPONSABLE DICIPLINE']);
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
