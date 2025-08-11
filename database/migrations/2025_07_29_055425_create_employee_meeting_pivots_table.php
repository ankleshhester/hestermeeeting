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
        Schema::create('employee_meeting_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_detail_id')->constrained('employee_details')->onDelete('cascade');
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->boolean('is_organizer')->default(false);
            $table->boolean('is_attending')->default(true);
            $table->timestamps();
            $table->dateTime('end_time')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_meeting_pivots');
    }
};
