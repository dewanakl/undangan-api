<?php

namespace App\Controllers\Api;

use App\Repositories\CommentContract;
use App\Repositories\CommentRepositories;
use App\Repositories\LikeContract;
use App\Request\UpdateUserRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Http\Stream;
use Core\Valid\Hash;

class DashboardController extends Controller
{
    private $json;

    public function __construct(JsonResponse $json)
    {
        $this->json = $json;
    }

    public function stats(CommentContract $comment, LikeContract $like): JsonResponse
    {
        $likes = $like->countLikeByUserID(Auth::id());
        $comments = $comment->countPresenceByUserID(Auth::id());

        return $this->json->successOK([
            'present' => $comments->present_count ?? 0,
            'absent' => $comments->absent_count ?? 0,
            'likes' => $likes,
        ]);
    }

    public function rotate(): JsonResponse
    {
        $status = Auth::user()
            ->only('id')
            ->fill([
                'access_key' => Hash::rand(25)
            ])
            ->save();

        if ($status == 1) {
            return $this->json->successStatusTrue();
        }

        return $this->json->errorServer();
    }

    public function user(): JsonResponse
    {
        return $this->json->successOK(Auth::user()->refresh()->except('password'));
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $valid = $request->validated();

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        $user = Auth::user()->only('id');

        if (!empty($valid->name)) {
            $user->name = $valid->name;
        }

        if (!empty($valid->filter)) {
            $user->is_filter = $valid->filter;
        }

        if (!empty($valid->get('old_password')) && !empty($valid->get('new_password'))) {
            if (!Hash::check($valid->get('old_password'), Auth::user()->refresh()->password)) {
                return $this->json->errorBadRequest(['password not match.']);
            }

            $user->password = Hash::make($valid->get('new_password'));
        }

        $status = $user->save();
        if ($status <= 1) {
            return $this->json->successStatusTrue();
        }

        return $this->json->errorServer();
    }

    public function download(Stream $stream, CommentRepositories $comment): Stream
    {
        fputcsv($stream->getStream(), [
            'uuid',
            'like',
            'name',
            'presence',
            'comment',
            'ip_address',
            'user_agent',
            'created_at',
            'parent_id',
        ]);

        foreach ($comment->downloadCommentByUserID(Auth::id()) as $value) {
            fputcsv(
                $stream->getStream(),
                array_values(get_object_vars($value))
            );
        }

        return $stream->create(sprintf('backup_comments_%s.csv', now('y-m-d_H:i:s')))->download();
    }
}
