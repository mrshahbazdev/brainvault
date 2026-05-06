<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class Bookmark extends Model
{
    use HasFactory, Searchable;

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
        'is_trashed',
        'trashed_at',
        'is_read_later',
        'read_later_reminder_at',
        'read_at',
        'read_progress',
        'metadata',
        'ai_summary',
        'ai_keywords',
        'ai_category',
        'scraped_at',
        'link_status',
        'link_checked_at',
        'snapshot_path',
        'snapshot_at',
        'share_token',
    ];

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'is_favorite' => 'boolean',
            'is_read' => 'boolean',
            'is_trashed' => 'boolean',
            'is_read_later' => 'boolean',
            'trashed_at' => 'datetime',
            'read_later_reminder_at' => 'datetime',
            'read_at' => 'datetime',
            'read_progress' => 'decimal:2',
            'metadata' => 'array',
            'ai_keywords' => 'array',
            'scraped_at' => 'datetime',
            'link_checked_at' => 'datetime',
            'snapshot_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_trashed', false);
    }

    public function scopeTrashed($query)
    {
        return $query->where('is_trashed', true);
    }

    public function scopeReadLater($query)
    {
        return $query->where('is_read_later', true)->where('is_trashed', false);
    }

    public function scopeBrokenLinks($query)
    {
        return $query->where('link_status', 'dead')->where('is_trashed', false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)
            ->withPivot('sort_order', 'added_at');
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

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'site_name' => $this->site_name,
            'excerpt' => $this->excerpt,
            'ai_summary' => $this->ai_summary,
            'ai_category' => $this->ai_category,
            'ai_keywords' => is_array($this->ai_keywords) ? implode(', ', $this->ai_keywords) : null,
        ];
    }

    public function searchableAs(): string
    {
        return 'bookmarks';
    }
}
