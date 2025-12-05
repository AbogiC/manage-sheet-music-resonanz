<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SheetMusicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'composer' => $this->composer,
            'arranger' => $this->arranger,
            'instrument' => $this->instrument,
            'genre' => $this->genre,
            'difficulty' => $this->difficulty,
            'pages' => $this->pages,
            'key' => $this->key,
            'time_signature' => $this->time_signature,
            'tempo' => $this->tempo,
            'description' => $this->description,
            'tags' => $this->tags,
            'file_url' => $this->file_url,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'thumbnail_url' => $this->thumbnail_url,
            'view_count' => $this->view_count,
            'download_count' => $this->download_count,
            'is_public' => $this->is_public,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
