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
            $table->increments('code_niveau')->primary();
            $table->string('label_niveau', 100);
            $table->text('desc_niveau', 256)->nullable();
            $table->string('code_filiere', 100);
            $table->foreign("code_filiere")->references("code_filiere")->on("filiere")->onDelete("cascade");
            //$table->foreignId('code_filiere')->constrained('filiere', 'code_filiere')->onDelete('cascade');
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
