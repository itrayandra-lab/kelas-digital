<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
 * Experts (class instructors and article authors) get their own table, picked from a dropdown instead of typed,
 * so one person is never stored under two spellings. The old text columns stay as the display name and follow
 * the expert's name. Existing data is linked by matching names without titles ("Wendra Wilendra M.MT." and
 * "Wendra Wilendra, M.MT" are the same person).
 */
return new class extends Migration
{
    /** The experts in the homepage group photo, with their schools (config/homepage.php families). */
    private const FEATURED = [
        ['Ria R. Christiana, MBA', ['dobetter-class', 'levelup-class']],
        ['Dr. apt. Soraya Ratnawulan Mita, M.Si', ['sekolah-kosmetik-indonesia']],
        ['apt. Zelika Mega Ramadhania, M.Sc, Ph.D', ['sekolah-kosmetik-indonesia']],
        ['Yusep Maulana, M.Kom, MBA', ['amaizing-class']],
        ['Prof. Dr. apt. Sriwidodo, M.Si', ['sekolah-kosmetik-indonesia']],
        ['apt. Cahya Khairani Kusumawulan, M.Farm', ['sekolah-kosmetik-indonesia']],
        ['Wendra Wilendra, M.MT', ['dobetter-class']],
        ['Prof. Dr. rer. nat. apt. Anis Yohana Chaerunisaa', ['sekolah-kosmetik-indonesia']],
    ];

    public function up(): void
    {
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('schools')->nullable();
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('expert_id')->nullable()->after('instructor')->constrained()->nullOnDelete();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('expert_id')->nullable()->after('author')->constrained()->nullOnDelete();
        });

        $this->linkExistingNames();
    }

    public function down(): void
    {
        Schema::table('articles', fn (Blueprint $table) => $table->dropConstrainedForeignId('expert_id'));
        Schema::table('courses', fn (Blueprint $table) => $table->dropConstrainedForeignId('expert_id'));
        Schema::dropIfExists('experts');
    }

    private function linkExistingNames(): void
    {
        $words = fn (string $name) => array_values(array_filter(preg_split('/\s+/', trim(explode(',', $name)[0])), fn ($w) => $w !== '' && ! str_contains($w, '.')));
        $known = []; // expert id => [name, words]

        $add = function (string $name, array $schools, bool $featured, int $order) use ($words, &$known): int {
            $base = Str::slug(implode(' ', $words($name)) ?: $name) ?: 'pakar';
            $slug = $base;
            for ($i = 2; DB::table('experts')->where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }
            $id = DB::table('experts')->insertGetId([
                'name' => $name, 'slug' => $slug, 'schools' => json_encode($schools), 'featured' => $featured, 'order' => $order,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $known[$id] = [$name, $words($name)];

            return $id;
        };

        foreach (self::FEATURED as $i => [$name, $schools]) {
            $add($name, $schools, true, $i + 1);
        }

        foreach (['courses' => 'instructor', 'articles' => 'author'] as $table => $column) {
            foreach (DB::table($table)->whereNotNull($column)->distinct()->pluck($column) as $text) {
                if (trim($text) === '') {
                    continue;
                }
                $id = collect($known)->search(fn ($e) => $e[1] && collect($e[1])->every(fn ($w) => mb_stripos($text, $w) !== false))
                    ?: $add(trim($text), [], false, 100);
                DB::table($table)->where($column, $text)->update(['expert_id' => $id, $column => $known[$id][0]]);
            }
        }
    }
};
