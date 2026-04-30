<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookmark_id',
        'parent_id',
        'title',
        'content',
        'content_html',
        'content_plain',
        'note_type',
        'is_pinned',
        'is_archived',
        'is_trashed',
        'trashed_at',
        'color',
        'cover_image',
        'word_count',
        'ai_summary',
        'ai_keywords',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_archived' => 'boolean',
            'is_trashed' => 'boolean',
            'trashed_at' => 'datetime',
            'ai_keywords' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Note::class, 'parent_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
