<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'research_project_id',
        'bookmark_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function researchProject(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class);
    }

    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
