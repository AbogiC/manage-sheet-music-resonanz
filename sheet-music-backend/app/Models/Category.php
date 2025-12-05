<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get categories by type.
     */
    public static function getByType($type)
    {
        return self::active()->ofType($type)->orderBy('order')->get();
    }

    /**
     * Get all instruments.
     */
    public static function getInstruments()
    {
        return self::getByType('instrument')->pluck('name')->toArray();
    }

    /**
     * Get all genres.
     */
    public static function getGenres()
    {
        return self::getByType('genre')->pluck('name')->toArray();
    }

    /**
     * Get all difficulties.
     */
    public static function getDifficulties()
    {
        return self::getByType('difficulty')->pluck('name')->toArray();
    }
}
