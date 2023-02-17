<?php

namespace App\Controllers;

use App\Models\Comment;
use Carbon\Carbon;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;
use Ramsey\Uuid\Uuid;

class CommentController extends Controller
{
    public function index()
    {
        $data = Comment::select(['nama', 'hadir', 'komentar', 'created_at'])
            ->where('user_id', context()->user->id)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($data as $key => $val) {
            $data->{$key}->created_at = Carbon::parse($val->created_at)->locale('id')->diffForHumans();
            $data->{$key}->nama = e($val->nama);
            $data->{$key}->komentar = e($val->komentar);
        }

        return json([
            'code' => 200,
            'data' => $data,
            'error' => []
        ]);
    }

    public function all(Request $request)
    {
        if ($request->get('id', '') !== env('JWT_KEY')) {
            return json([
                'code' => 401,
                'data' => [],
                'error' => ['unauthorized']
            ], 401);
        }

        $data = Comment::orderBy('id', 'DESC')->get();

        foreach ($data as $key => $val) {
            $data->{$key}->created_at = Carbon::parse($val->created_at)->locale('id')->diffForHumans();
            $data->{$key}->nama = e($val->nama);
            $data->{$key}->komentar = e($val->komentar);
        }

        return json([
            'code' => 200,
            'data' => $data,
            'error' => []
        ]);
    }

    public function destroy(string $id)
    {
        $data = Comment::where('uuid', $id)
            ->where('user_id', context()->user->id)
            ->limit(1)
            ->first()
            ->fail(fn () => false);

        if ($data === false) {
            return json([
                'code' => 404,
                'data' => [],
                'error' => ['not found']
            ], 404);
        }

        $status = Comment::id($data->id)->delete();

        return json([
            'code' => 200,
            'data' => [
                'status' => $status
            ],
            'error' => []
        ]);
    }

    public function create(Request $request)
    {
        $valid = Validator::make(
            array_merge(
                $request->only(['nama', 'hadir', 'komentar']),
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->server('HTTP_USER_AGENT')
                ]
            ),
            [
                'nama' => ['required', 'str', 'max:50'],
                'hadir' => ['bool'],
                'komentar' => ['required', 'str', 'max:1000'],
                'user_agent' => ['str', 'trim'],
                'ip' => ['str', 'trim', 'max:50']
            ]
        );

        if ($valid->fails()) {
            return json([
                'code' => 400,
                'data' => [],
                'error' => $valid->messages()
            ], 400);
        }

        $data = $valid->get();
        $data['uuid'] = Uuid::uuid4()->toString();
        $data['user_id'] = context()->user->id;

        $result = Comment::create($data)->except(['uuid', 'id', 'user_id', 'user_agent', 'ip', 'updated_at']);

        return json([
            'code' => 201,
            'data' => $result,
            'error' => []
        ], 201);
    }
}
