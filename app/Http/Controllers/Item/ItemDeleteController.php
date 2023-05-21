<?php

namespace App\Http\Controllers\Item;

use App\Actions\Item\ItemDeleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemDeleteController extends Controller
{
    public function __construct(
        private readonly ItemDeleteAction $itemDeleteAction
    ) {
    }

    /**
     * @OA\Delete(
     *     path="/item/{itemId}",
     *     summary="Delete an item",
     *     tags={"Item"},
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         description="Item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item deleted"
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
    public function __invoke(int $id): JsonResponse|HttpResponse
    {
        try {
            ($this->itemDeleteAction)($id);

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
