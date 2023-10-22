<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Response\JsonResponse;
use Core\Database\DB;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;
use Ramsey\Uuid\Uuid;
use Throwable;

class CommentController extends Controller
{
    private $json;

    public function __construct(JsonResponse $json)
    {
        $this->json = $json;
    }

    public function get(Request $request): JsonResponse
    {
        $valid = $this->validate($request, [
            'next' => ['nullable', 'int'],
            'per' => ['required', 'int', 'max:50']
        ]);

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::with('comments')
            ->select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])
            ->where('user_id', context()->user->id)
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC')
            ->limit(abs($valid->per))
            ->offset($valid->next ?? 0)
            ->get();

        return $this->json->success($data, 200);
    }

    public function show(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('uuid', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->select(['nama', 'hadir', 'komentar', 'created_at'])
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        return $this->json->success($data, 200);
    }

    public function like(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('uuid', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $like = Like::create([
            'uuid' => Uuid::uuid4()->toString(),
            'comment_id' => $data->uuid,
            'user_id' => context()->user->id
        ]);

        return $this->json->success($like->only('uuid'), 201);
    }

    public function unlike(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Like::where('uuid', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $status = $data->destroy();

        if ($status == 1) {
            return $this->json->success([
                'status' => true
            ], 200);
        }

        return $this->json->error(['server error'], 500);
    }

    public function destroy(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('own', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        try {
            DB::beginTransaction();

            Like::where('comment_id', $data->uuid)->delete();
            Comment::where('parent_id', $data->uuid)->delete();

            DB::commit();
        } catch (Throwable) {
            DB::rollBack();
            return $this->json->error(['server error'], 500);
        }

        $status = $data->destroy();

        if ($status == 1) {
            return $this->json->success([
                'status' => true
            ], 200);
        }

        return $this->json->error(['server error'], 500);
    }

    public function update(string $id, Request $request): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id,
                ...$request->only(['hadir', 'komentar'])
            ],
            [
                'id' => ['required', 'str', 'trim', 'uuid', 'max:37'],
                'hadir' => ['bool'],
                'komentar' => ['required', 'str', 'max:500'],
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('own', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->select(['id', 'hadir', 'komentar'])
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $status = $data->fill($valid->only(['hadir', 'komentar']))->save();

        if ($status == 1) {
            return $this->json->success([
                'status' => true
            ], 200);
        }

        return $this->json->error(['server error'], 500);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make(
            [
                ...$request->only(['id', 'nama', 'hadir', 'komentar']),
                'ip' => env('HTTP_CF_CONNECTING_IP') ? $request->server->get('HTTP_CF_CONNECTING_IP') : $request->ip(),
                'user_agent' => $request->server->get('HTTP_USER_AGENT')
            ],
            [
                'id' => ['nullable', 'str', 'trim', 'uuid', 'max:37'],
                'nama' => ['required', 'str', 'trim', 'max:50'],
                'hadir' => ['bool'],
                'komentar' => ['required', 'str', 'max:500'],
                'ip' => ['nullable', 'str', 'trim', 'max:50'],
                'user_agent' => ['nullable', 'str', 'trim', 'max:500']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = $valid->except(['id']);
        $data['parent_id'] = $valid->id;
        $data['uuid'] = Uuid::uuid4()->toString();
        $data['own'] = Uuid::uuid4()->toString();
        $data['user_id'] = context()->user->id;

        $comment = Comment::create($data);

        return $this->json->success(
            $comment->only(['nama', 'hadir', 'komentar', 'uuid', 'own', 'created_at']),
            201
        );
    }
}
