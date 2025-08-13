<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_id')->unique();
            $table->string('complainant_name');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('user_id')->nullable(); // New: Links to users
            $table->text('complaint_text');
            $table->enum('status', ['Pending', 'In Progress', 'Resolved'])->default('Pending');
            $table->string('category')->nullable(); // Added for role-specific categories

            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}
