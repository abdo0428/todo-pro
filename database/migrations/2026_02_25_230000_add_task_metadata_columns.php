<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('due_date');
            $table->string('notes', 500)->nullable()->after('priority');

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn(['due_date', 'priority', 'notes']);
        });
    }
};
