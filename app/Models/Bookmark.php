<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'title',
        'description',
        'excerpt',
        'favicon_url',
        'og_image_url',
        'screenshot_path',
        'site_name',
        'content_type',
        'reading_time',
        'word_count',
        'is_archived',
        'is_favorite',
        'is_read',
        'read_at',
        'read_progress',
        'metadata',
        'ai_summary',
        'ai_keywords',
        'ai_category',
        'scraped_at',
    ];

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'is_favorite' => 'boolean',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'read_progress' => 'decimal:2',
            'metadata' => 'array',
            'ai_keywords' => 'array',
            'scraped_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)
            ->withPivot('sort_order', 'added_at')
            ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
