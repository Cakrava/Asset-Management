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
Schema::create('transactions', function (Blueprint $table) {
    $table->id();

    $table->string('unique_id');
    $table->string('transaction_id', 50);
    $table->enum('instalation_status',['Deployed', 'Pending','Intake','Revoked ']); // "Installed", "Not Installed"
    $table->enum('transaction_type', ['in', 'out']);
    $table->unsignedBigInteger('client_id')->nullable();
    $table->unsignedBigInteger('other_source_id')->nullable();
    $table->integer('letter_id')->nullable();
    $table->timestamps();

    $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
