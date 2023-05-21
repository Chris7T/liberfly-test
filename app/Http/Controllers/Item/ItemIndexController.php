<?php

namespace App\Http\Controllers\Item;

use App\Actions\Item\ItemListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Item\ItemRegisterResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ItemIndexController extends Controller
{
    public function __construct(
        private readonly ItemListAction $itemListAction
    ) {
    }

    /**
     * @OA\Get(
     *     path="/items",
     *     summary="Retrieve a collection of items",
     *     tags={"Item"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ItemResource")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="to", type="integer", example=10),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="last_page", type="integer", example=10),
     *             @OA\Property(property="path", type="string", example="/items"),
     *             @OA\Property(property="first_page_url", type="string", example="/items?page=1"),
     *             @OA\Property(property="last_page_url", type="string", example="/items?page=10"),
     *             @OA\Property(property="next_page_url", type="string", example="/items?page=2"),
     *             @OA\Property(property="prev_page_url", type="string", example=null),
     *             @OA\Property(property="links", type="array", @OA\Items(type="object"))
     *         )
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

    public function __invoke(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            return ItemRegisterResource::collection(($this->itemListAction)($request->input('page', 1)));
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
