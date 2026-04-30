<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'color',
        'usage_count',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks(): MorphToMany
    {
        return $this->morphedByMany(Bookmark::class, 'taggable');
    }

    public function notes(): MorphToMany
    {
        return $this->morphedByMany(Note::class, 'taggable');
    }

    public function highlights(): MorphToMany
    {
        return $this->morphedByMany(Highlight::class, 'taggable');
    }
}
