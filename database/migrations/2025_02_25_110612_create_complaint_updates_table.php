<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintUpdatesTable extends Migration
{
    public function up()
    {
        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->enum('status', ['Pending', 'In Progress', 'Resolved']);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); // This is correct
            $table->timestamps();

            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_updates');
    }
}
