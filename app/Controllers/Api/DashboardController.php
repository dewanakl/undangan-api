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
        $comments = $comment->countPresenceByUserID(Auth::id());

        return $this->json->successOK([
            'present' => intval($comments->present_count ?? 0),
            'absent' => intval($comments->absent_count ?? 0),
            'likes' => $like->countLikeByUserID(Auth::id()),
            'comments' => $comment->countCommentByUserID(Auth::id())
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

    public function config(): JsonResponse
    {
        return $this->json->successOK(Auth::user()->refresh()->only(['name', 'can_edit', 'can_delete', 'can_reply']));
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

        if ($valid->get('filter') !== null) {
            $user->is_filter = boolval($valid->filter);
        }

        if ($valid->get('can_edit') !== null) {
            $user->can_edit = boolval($valid->can_edit);
        }

        if ($valid->get('can_delete') !== null) {
            $user->can_delete = boolval($valid->can_delete);
        }

        if ($valid->get('can_reply') !== null) {
            $user->can_reply = boolval($valid->can_reply);
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
            'is_admin',
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
