<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class FileSharedResource extends JsonResource
{
    public static function collection($resource): AnonymousResourceCollection
    {
        self::$wrap = null;
        return parent::collection($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'file_id' => $this->file_id,
            'name' => $this->name,
            'url' => config('app.url')."/files/{$this->file_id}",
        ];
    }
}
