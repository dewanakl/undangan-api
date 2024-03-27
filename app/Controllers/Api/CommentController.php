<?php

namespace App\Controllers\Api;

use App\Middleware\UuidMiddleware;
use App\Repositories\CommentContract;
use App\Repositories\LikeContract;
use App\Request\InsertCommentRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Database\DB;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Http\Respond;
use Kamu\Aman;
use Throwable;

class CommentController extends Controller
{
    private $comment;
    private $json;

    public function __construct(CommentContract $comment, JsonResponse $json)
    {
        $this->json = $json;
        $this->comment = $comment;
    }

    public function get(Request $request): JsonResponse
    {
        $valid = $this->validate($request, [
            'next' => ['nullable', 'int'],
            'per' => ['required', 'int', 'max:10']
        ]);

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        return $this->json->successOK($this->comment->getAll(
            Auth::id(),
            $valid->per,
            ($valid->next ?? 0)
        ));
    }

    #[UuidMiddleware]
    public function show(string $id): JsonResponse
    {
        $comment = $this->comment->getByUuid(Auth::id(), $id);

        if (!$comment->exist()) {
            return $this->json->errorNotFound();
        }

        return $this->json->successOK($comment->only(['name', 'presence', 'comment', 'is_admin', 'created_at']));
    }

    #[UuidMiddleware]
    public function like(string $id, LikeContract $like): JsonResponse
    {
        $comment = $this->comment->getByUuid(Auth::id(), $id);

        if (!$comment->exist()) {
            return $this->json->errorNotFound();
        }

        return $this->json->success(
            $like->create(Auth::id(), $comment->uuid)->only('uuid'),
            Respond::HTTP_CREATED
        );
    }

    #[UuidMiddleware]
    public function unlike(string $id, LikeContract $like): JsonResponse
    {
        $like = $like->getByUuid(Auth::id(), $id);

        if (!$like->exist()) {
            return $this->json->errorNotFound();
        }

        if ($like->destroy() == 1) {
            return $this->json->successStatusTrue();
        }

        return $this->json->errorServer();
    }

    #[UuidMiddleware]
    public function destroy(string $id): JsonResponse
    {
        if (!boolval(Auth::user()->can_delete) && !boolval(Auth::user()->is_admin)) {
            return $this->json->errorBadRequest(['permission is not allowed']);
        }

        $comment = $this->comment->getByOwnId(Auth::id(), $id);

        if (!$comment->exist()) {
            return $this->json->errorNotFound();
        }

        try {
            $status = DB::transaction(function (LikeContract $like) use ($comment): int {
                $like->deleteByCommentID($comment->uuid);
                $this->comment->deleteByParentID($comment->uuid);

                return $comment->destroy();
            });

            if ($status == 1) {
                return $this->json->successStatusTrue();
            }

            return $this->json->errorServer();
        } catch (Throwable) {
            return $this->json->errorServer();
        }
    }

    #[UuidMiddleware]
    public function update(string $id, Request $request): JsonResponse
    {
        if (!boolval(Auth::user()->can_edit) && !boolval(Auth::user()->is_admin)) {
            return $this->json->errorBadRequest(['permission is not allowed']);
        }

        $valid = $this->validate($request, [
            'presence' => ['bool'],
            'comment' => ['required', 'str', 'min:3', 'max:500'],
        ]);

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        $comment = $this->comment->getByOwnId(Auth::id(), $id);

        if (!$comment->exist()) {
            return $this->json->errorNotFound();
        }

        if (!empty(Auth::user()->is_filter)) {
            $valid->comment = Aman::factory()->masking($valid->comment, ' * ');
        }

        $status = $comment->only(['id', 'presence', 'comment'])
            ->fill($valid->only(['presence', 'comment']))
            ->save();

        if ($status == 1) {
            return $this->json->successStatusTrue();
        }

        return $this->json->errorServer();
    }

    public function create(InsertCommentRequest $request): JsonResponse
    {
        $valid = $request->validated();

        if (!boolval(Auth::user()->can_reply) && $valid->get('id') !== null && !boolval(Auth::user()->is_admin)) {
            return $this->json->errorBadRequest(['permission is not allowed']);
        }

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        if (!empty(Auth::user()->is_filter)) {
            $valid->comment = Aman::factory()->masking($valid->comment, ' * ');
        }

        $comment = $this->comment->create([
            ...$valid->except(['id']),
            'user_id' => Auth::id(),
            'parent_id' => $valid->id,
            'is_admin' => !empty(auth()->user()->is_admin)
        ]);

        return $this->json->success(
            $comment->only(['name', 'presence', 'comment', 'uuid', 'own', 'created_at']),
            Respond::HTTP_CREATED
        );
    }
}
