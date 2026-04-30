<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Topic extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'ai_generated',
        'item_count',
    ];

    protected function casts(): array
    {
        return [
            'ai_generated' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Topic $topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
