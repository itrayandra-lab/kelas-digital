<?php

use App\Models\EventRegistration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('code', 12)->nullable()->unique()->after('id');
            $table->timestamp('checked_in_at')->nullable()->after('age');
        });

        // Registrations made before tickets existed get a code too.
        DB::table('event_registrations')->whereNull('code')->pluck('id')->each(
            fn ($id) => DB::table('event_registrations')->where('id', $id)->update(['code' => EventRegistration::newCode()])
        );
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'checked_in_at']);
        });
    }
};
