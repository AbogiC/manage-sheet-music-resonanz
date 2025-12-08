<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SheetMusic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sheet_music';

    protected $fillable = [
        'user_id',
        'title',
        'composer',
        'arranger',
        'instrument',
        'genre',
        'difficulty',
        'pages',
        'key',
        'time_signature',
        'tempo',
        'description',
        'tags',
        'file_path',
        'file_name',
        'file_size',
        'thumbnail_path',
        'view_count',
        'download_count',
        'is_public'
    ];

    protected $casts = [
        'tags' => 'array',
        'pages' => 'integer',
        'tempo' => 'integer',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'is_public' => 'boolean'
    ];

    protected $appends = ['file_url', 'thumbnail_url'];

    /**
     * Get the user that owns the sheet music.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * Scope a query to only include public sheet music.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to filter by instrument.
     */
    public function scopeByInstrument($query, $instrument)
    {
        return $query->where('instrument', $instrument);
    }

    /**
     * Scope a query to filter by genre.
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Scope a query to filter by difficulty.
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope a query to filter by event.
     */
    public function scopeByEvent($query, $eventId)
    {
        return $query->whereHas('events', function($q) use ($eventId) {
            $q->where('events.id', $eventId);
        });
    }

    /**
     * Scope a query to search in title, composer, or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('composer', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Increment download count.
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Get the events this sheet music is associated with.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_score')
                    ->withPivot('order', 'notes')
                    ->withTimestamps()
                    ->orderBy('event_score.order');
    }
}
