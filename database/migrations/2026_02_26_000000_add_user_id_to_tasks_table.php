<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index(['user_id', 'status', 'created_at']);
        });

        $firstUserId = User::query()->value('id');

        if (! $firstUserId) {
            $firstUserId = User::query()->create([
                'name' => 'Legacy User',
                'email' => 'legacy@example.com',
                'password' => Hash::make('password123'),
            ])->id;
        }

        DB::table('tasks')->whereNull('user_id')->update(['user_id' => $firstUserId]);

    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status', 'created_at']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
