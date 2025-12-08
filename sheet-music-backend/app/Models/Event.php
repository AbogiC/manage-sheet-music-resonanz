<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'location',
        'user_id'
    ];

    protected $casts = [
        'event_date' => 'datetime'
    ];

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sheet music associated with this event.
     */
    public function sheetMusic()
    {
        return $this->belongsToMany(SheetMusic::class, 'event_score')
                    ->withPivot('order', 'notes')
                    ->withTimestamps()
                    ->orderBy('event_score.order');
    }
}
