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
        Schema::create('programmation', function (Blueprint $table) {
            $table->string("code_ec", 20);
            $table->string("num_salle", 20);
            $table->string("code_pers", 20);
            $table->foreign("code_ec")->references("code_ec")->on("ec");
            $table->foreign("num_salle")->references("num_salle")->on("salle");
            $table->foreign("code_pers")->references("code_pers")->on("personnel");
            $table->date("date");
            $table->dateTime("date-debut");
            $table->dateTime("date_fin");
            $table->integer("nbre_heure");
            $table->enum("statut", ["EN COURS", "EN ATTENTE", "ACHEVER"]);
            $table->primary(["code_ec", "num_salle", "code_pers"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmation');
    }
};
