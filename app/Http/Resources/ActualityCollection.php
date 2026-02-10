<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActualityCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cover_path' => $this->cover_path,
            'description' => $this->description,
            'title' => $this->title,
            'category' => new CategoryCollection($this->category),
            'created_at' => $this->published_at 
                ? $this->published_at->format('Y-m-d') 
                : (new DateTime($this->created_at))->format('Y-m-d'),
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d'),
            'additional_images' => GalleryCollection::collection($this->galleries)
        ];
    }
}