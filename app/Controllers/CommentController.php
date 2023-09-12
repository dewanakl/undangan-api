<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Response\JsonResponse;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;
use Ramsey\Uuid\Uuid;

class CommentController extends Controller
{
    private $json;

    public function __construct(JsonResponse $json)
    {
        $this->json = $json;
    }

    public function index(Request $request): JsonResponse
    {
        $valid = $this->validate($request, [
            'next' => ['max:3'],
            'per' => ['max:3']
        ]);

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $valid->next = intval($valid->next);
        $valid->per = intval($valid->per);

        $data = Comment::with('comments')
            ->select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])
            ->where('user_id', context()->user->id)
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC');

        if ($valid->next >= 0 && $valid->per > 0) {
            $data = $data->limit($valid->per)->offset($valid->next);
        }

        return $this->json->success($data->get(), 200);
    }

    public function all(Request $request): JsonResponse
    {
        if ($request->get('id', '') !== env('JWT_KEY')) {
            return $this->json->error(['unauthorized'], 401);
        }

        return $this->json->success(Comment::orderBy('id', 'DESC')->get(), 200);
    }

    public function show(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('uuid', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->select(['nama', 'komentar', 'created_at'])
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
                'id' => ['required', 'str', 'trim', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Comment::where('uuid', $valid->id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->select('uuid')
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        return $this->json->success(Like::create([
            'uuid' => Uuid::uuid4()->toString(),
            'comment_id' => $data->uuid
        ])->only('uuid'), 201);
    }

    public function unlike(string $id): JsonResponse
    {
        $valid = Validator::make(
            [
                'id' => $id
            ],
            [
                'id' => ['required', 'str', 'trim', 'max:37']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = Like::where('uuid', $valid->id)
            ->select('id')
            ->limit(1)
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $status = Like::where('id', $data->id)->delete() == 1;

        if ($status) {
            return $this->json->success([
                'status' => $status
            ], 200);
        }

        return $this->json->error([
            ['server error']
        ], 500);
    }

    public function destroy(string $id, Request $request): JsonResponse
    {
        if ($request->get('id', '') !== env('JWT_KEY')) {
            return $this->json->error(['unauthorized'], 401);
        }

        $data = Comment::where('uuid', $id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->exist();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $status = Comment::id($data->id)->delete() == 1;

        if ($status) {
            return $this->json->success([
                'status' => $status
            ], 200);
        }

        return $this->json->error([
            ['server error']
        ], 500);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make(
            [
                ...$request->only(['id', 'nama', 'hadir', 'komentar']),
                'ip' => $request->ip(),
                'user_agent' => $request->server->get('HTTP_USER_AGENT')
            ],
            [
                'id' => ['str', 'trim', 'max:37'],
                'nama' => ['required', 'str', 'max:50'],
                'hadir' => ['bool'],
                'komentar' => ['required', 'str', 'max:500'],
                'user_agent' => ['str', 'trim'],
                'ip' => ['str', 'trim', 'max:50']
            ]
        );

        if ($valid->fails()) {
            return $this->json->error($valid->messages(), 400);
        }

        $data = $valid->except(['id']);
        $data['parent_id'] = empty($valid->id) ? null : $valid->id;
        $data['uuid'] = Uuid::uuid4()->toString();
        $data['user_id'] = context()->user->id;

        return $this->json->success(Comment::create($data)->except(['uuid', 'parent_id', 'id', 'user_id', 'user_agent', 'ip', 'updated_at']), 201);
    }
}
