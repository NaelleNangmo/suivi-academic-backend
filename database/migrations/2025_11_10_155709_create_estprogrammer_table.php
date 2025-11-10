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
        Schema::create('estprogrammer', function (Blueprint $table) {
            
            $table->date('date');
            $table->dateTime('heure_debut');
            $table->dateTime('heure_fin');
            $table->string('statut', 20);

            $table->string('code_ec', 20);
            $table->string('num_sale', 20);
            $table->string('code_pers', 20);
            
            $table->primary (['code_ec', 'num_sale', 'code_pers']);
           
            $table->foreign('code_ec')->references('code_ec')->on('ec')->onDelete('cascade');
            $table->foreign('num_sale')->references('num_sale')->on('sale')->onDelete('cascade');
            $table->foreign('code_pers')->references('code_pers')->on('personnel')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estprogrammer');
    }
};
