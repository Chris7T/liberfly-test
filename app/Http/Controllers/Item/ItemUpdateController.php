<?php

namespace App\Http\Controllers\Item;

use App\Actions\Item\ItemUpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemUpdateController extends Controller
{
    public function __construct(
        private readonly ItemUpdateAction $itemUpdateAction
    ) {
    }

    /**
     * @OA\Put(
     *     path="/item/{itemId}",
     *     summary="Update an existing item",
     *     tags={"Item"},
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         description="Item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Item name"),
     *             @OA\Property(property="description", type="string", example="Item description"),
     *         )
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
     *         response=204,
     *         description="Item updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item not found")
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
    public function __invoke(ItemRegisterRequest $request, int $itemId): HttpResponse|JsonResponse
    {
        try {
            ($this->itemUpdateAction)($itemId, $request->validated());

            return Response::noContent();
        } catch (NotFoundHttpException $ex) {
            return Response::json(
                ['message' => $ex->getMessage()],
                $ex->getStatusCode(),
            );
        } catch (\Exception $ex) {
            Log::critical('Controller: ' . self::class, ['exception' => $ex->getMessage()]);

            return Response::json(
                ['message' => config('messages.error.server')],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
