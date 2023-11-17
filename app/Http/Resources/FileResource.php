<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{

    public static function collection($resource): AnonymousResourceCollection
    {
        self::$wrap = null;
        return parent::collection($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'file_id' => $this->file_id,
            'url' => config('app.url') . '/files/' . $this->file_id
        ];
    }
}
