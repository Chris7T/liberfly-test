<?php

namespace App\Http\Controllers\Item;

use App\Actions\Item\ItemRegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemRegisterRequest;
use App\Http\Resources\Item\ItemRegisterResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ItemRegisterController extends Controller
{
    public function __construct(
        private readonly ItemRegisterAction $itemRegisterAction
    ) {
    }

    /**
     * @OA\Post(
     *     path="/item",
     *     summary="Create a new item",
     *     tags={"Item"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Item name"),
     *             @OA\Property(property="description", type="string", example="Item description"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item created",
     *         @OA\JsonContent(ref="#/components/schemas/ItemResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(property="description", type="array",
     *                     @OA\Items(type="string", example="The description field is required.")
     *                 ),
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function __invoke(ItemRegisterRequest $request): JsonResponse|ItemRegisterResource
    {
        try {
            $items = ($this->itemRegisterAction)($request->validated());
            return ItemRegisterResource::make($items)->response()->setStatusCode(HttpResponse::HTTP_CREATED);
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
