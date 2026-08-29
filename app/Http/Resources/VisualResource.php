<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisualResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'url' => route('visuals.show', $this->slug),
            'render_url' => route('visuals.render', $this->slug),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'file' => $this->whenLoaded('file', function () {
                return [
                    'name' => $this->file?->origin_name,
                    'size' => $this->file?->file_size,
                    'url' => $this->file?->url,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
