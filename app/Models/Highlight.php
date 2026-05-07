<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Highlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookmark_id',
        'task_id',
        'text',
        'note',
        'color',
        'page_url',
        'start_xpath',
        'start_offset',
        'end_xpath',
        'end_offset',
        'surrounding_text',
        'screenshot_path',
        'sort_order',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
