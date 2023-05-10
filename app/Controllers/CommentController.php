<?php

namespace App\Controllers;

use App\Models\Comment;
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

    private function getInnerComment(string $id)
    {
        return Comment::select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])
            ->where('user_id', context()->user->id)
            ->where('parent_id', $id)
            ->orderBy('id')
            ->get()
            ->map(
                function ($val) {
                    $val->created_at = $val->created_at->diffForHumans();
                    $val->comment = $this->getInnerComment($val->uuid);
                    return $val;
                }
            );
    }

    public function index(Request $request)
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

        $data = Comment::select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])
            ->where('user_id', context()->user->id)
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC');

        if ($valid->next >= 0 && $valid->per > 0) {
            $data = $data->limit($valid->per)->offset($valid->next);
        }

        $data = $data->get()
            ->map(
                function ($val) {
                    $val->created_at = $val->created_at->diffForHumans();
                    $val->comment = $this->getInnerComment($val->uuid);
                    return $val;
                }
            );

        return $this->json->success($data, 200);
    }

    public function all(Request $request)
    {
        if ($request->get('id', '') !== env('JWT_KEY')) {
            return $this->json->error(['unauthorized'], 401);
        }

        $data = Comment::orderBy('id', 'DESC')
            ->get()
            ->map(
                function ($val) {
                    $val->created_at = $val->created_at->diffForHumans();
                    return $val;
                }
            );

        return $this->json->success($data, 200);
    }

    public function show(string $id)
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
            ->fail();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $data->created_at = $data->created_at->diffForHumans();

        return $this->json->success($data, 200);
    }

    public function destroy(string $id, Request $request)
    {
        if ($request->get('id', '') !== env('JWT_KEY')) {
            return $this->json->error(['unauthorized'], 401);
        }

        $data = Comment::where('uuid', $id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->fail();

        if (!$data) {
            return $this->json->error(['not found'], 404);
        }

        $status = Comment::id($data->id)->delete();

        return $this->json->success([
            'status' => $status == 1
        ], 200);
    }

    public function create(Request $request)
    {
        $valid = Validator::make(
            array_merge(
                $request->only(['id', 'nama', 'hadir', 'komentar']),
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->server('HTTP_USER_AGENT')
                ]
            ),
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

        $data = Comment::create($data)->except(['uuid', 'parent_id', 'id', 'user_id', 'user_agent', 'ip', 'updated_at']);
        $data->created_at = $data->created_at->diffForHumans();

        return $this->json->success($data, 201);
    }
}
