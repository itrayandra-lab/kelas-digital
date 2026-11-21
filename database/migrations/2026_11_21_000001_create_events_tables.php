<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('badge', 60)->nullable();
            $table->string('cover')->nullable();
            $table->text('description')->nullable();
            $table->string('format', 10)->default('offline'); // offline | online
            $table->date('event_date');
            $table->string('timezone', 4)->default('WIB');
            $table->string('venue')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->string('meeting_url', 500)->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('registration_open')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'event_date']);
        });

        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('label', 60);
            $table->time('starts_at');
            $table->time('ends_at')->nullable();
            $table->unsignedInteger('capacity');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('whatsapp', 20);
            $table->unsignedTinyInteger('age');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('institution');
        });

        // Live servers don't rerun the seeder, so the permission is created here and given to admins.
        Permission::findOrCreate('manage events', 'web');
        Role::where('name', 'admin')->first()?->givePermissionTo('manage events');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('phone'));
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_sessions');
        Schema::dropIfExists('events');
        Permission::where('name', 'manage events')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
