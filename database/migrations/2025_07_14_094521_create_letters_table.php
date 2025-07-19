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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('letter_number');
            $table->string('subject')->default('SST');
            $table->enum('status', ['Open', 'Closed','Deleted','Needed']);
            $table->string('pdf_path')->nullable();
            $table->string('sign_pdf_path')->nullable();
            $table->integer('client_id');
            $table->integer('ticket_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
