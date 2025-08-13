<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Complaint;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to complaints table (nullable)
        Schema::table('complaints', function (Blueprint $table) {
            $table->bigInteger('category_id')->unsigned()->nullable()->after('user_id');
            $table->string('title')->nullable()->after('category');
            $table->string('priority')->nullable()->after('title');
            $table->json('additional_data')->nullable()->after('status');

            // Add foreign key constraint
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // Update existing rows
        $categories = Category::all()->pluck('id', 'name')->toArray();
        if (!empty($categories)) {
            Complaint::all()->each(function ($complaint) use ($categories) {
                // Set category_id
                $complaint->category_id = isset($categories[$complaint->category]) 
                    ? $categories[$complaint->category] 
                    : Category::first()->id ?? null;

                // Set title (first 255 chars of complaint_text or default)
                $complaint->title = $complaint->complaint_text 
                    ? substr($complaint->complaint_text, 0, 255) 
                    : 'Untitled Complaint';

                // Set priority
                $complaint->priority = 'low';

                $complaint->save();
            });
        }

        // Enforce NOT NULL constraints
        if (Complaint::whereNull('category_id')->doesntExist() && 
            Complaint::whereNull('title')->doesntExist() && 
            Complaint::whereNull('priority')->doesntExist()) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->bigInteger('category_id')->unsigned()->nullable(false)->change();
                $table->string('title')->nullable(false)->change();
                $table->string('priority')->nullable(false)->change();
            });
        } else {
            \Illuminate\Support\Facades\Log::warning('Some complaints have null values for category_id, title, or priority after migration.');
        }
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'title', 'priority', 'additional_data']);
        });
    }
};