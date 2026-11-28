<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A class instructor or article author. Courses and articles pick one from a dropdown; their old `instructor` and
 * `author` text columns are kept as the display name and follow this name. `schools` holds Learning Family slugs
 * (config/homepage.php); `featured` experts are the ones in the homepage group photo.
 */
class Expert extends Model
{
    protected $fillable = ['name', 'schools', 'featured', 'order'];

    protected $casts = ['schools' => 'array', 'featured' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (Expert $expert) {
            $base = Str::slug(implode(' ', self::nameWords($expert->name)) ?: $expert->name) ?: 'pakar';
            $slug = $base;
            for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }
            $expert->slug = $slug;
        });

        // A renamed expert is renamed everywhere their name is shown.
        static::updated(function (Expert $expert) {
            if ($expert->wasChanged('name')) {
                Course::where('expert_id', $expert->id)->update(['instructor' => $expert->name]);
                Article::where('expert_id', $expert->id)->update(['author' => $expert->name]);
            }
        });
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function initials(): string
    {
        $words = self::nameWords($this->name) ?: [$this->name];

        return mb_strtoupper(mb_substr($words[0], 0, 1).(count($words) > 1 ? mb_substr(end($words), 0, 1) : ''));
    }

    /** "Prof. Dr. apt. Sriwidodo, M.Si" → ["Sriwidodo"]: the part before the degrees, without titles or initials. */
    public static function nameWords(string $name): array
    {
        $words = preg_split('/\s+/', trim(explode(',', $name)[0]));

        return array_values(array_filter($words, fn ($w) => $w !== '' && ! str_contains($w, '.')));
    }
}
