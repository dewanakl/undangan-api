<?php

namespace App\Controllers;

use App\Models\Comment;
use Carbon\Carbon;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Valid\Validator;

class CommentController extends Controller
{
    public function index()
    {
        $data = Comment::orderBy('id', 'DESC')->get();

        foreach ($data as $key => $val) {
            $date = Carbon::parse($val->created_at)->locale('id');
            $data->{$key}->created_at = $date->diffForHumans();
        }

        return json([
            'code' => 200,
            'data' => $data,
            'error' => null
        ]);
    }

    public function destroy($id)
    {
        $data = Comment::find($id)->fail(fn () => false);

        if ($data === false) {
            return json([
                'code' => 404,
                'data' => [],
                'error' => 'not found'
            ], 404);
        }

        $status = Comment::destroy($id);

        return json([
            'code' => 200,
            'data' => [
                'status' => $status
            ],
            'error' => null
        ]);
    }

    public function create(Request $request)
    {
        $valid = Validator::make($request->only(['nama', 'hadir', 'komentar']), [
            'nama' => ['required', 'str', 'max:50'],
            'hadir' => ['bool'],
            'komentar' => ['required', 'str']
        ]);

        if ($valid->fails()) {
            return json([
                'code' => 400,
                'data' => [],
                'error' => $valid->failed()
            ], 400);
        }

        $data = $valid->get();
        $data['user_id'] = context()->user->id;

        $result = Comment::create($data);

        return json([
            'code' => 201,
            'data' => $result,
            'error' => null
        ], 201);
    }
}
