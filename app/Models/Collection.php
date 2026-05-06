<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Team;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'cover_image',
        'is_default',
        'is_smart',
        'smart_rules',
        'sort_order',
        'visibility',
        'share_slug',
        'item_count',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_smart' => 'boolean',
            'smart_rules' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Collection::class, 'parent_id');
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Bookmark::class)
            ->withPivot('sort_order', 'added_at');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'collection_team')
            ->withPivot('permission', 'shared_by')
            ->withTimestamps();
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public' && !empty($this->share_slug);
    }
}
