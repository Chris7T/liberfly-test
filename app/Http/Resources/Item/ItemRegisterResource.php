<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ItemRegisterResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="ItemResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer", description="Item ID", example="Item ID"),
     *     @OA\Property(property="name", type="string", description="Item name", example="Item name"),
     *     @OA\Property(property="description", type="string", description="Item description", example="Item description"),
     *     @OA\Property(property="user_id", type="integer", description="User ID", example="User ID"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Arr::get($this, 'id'),
            'name' => Arr::get($this, 'name'),
            'description' => Arr::get($this, 'description'),
            'user_id' => Arr::get($this, 'user_id')
        ];
    }
}
