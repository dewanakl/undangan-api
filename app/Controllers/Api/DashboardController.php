<?php

namespace App\Controllers\Api;

use App\Repositories\CommentContract;
use App\Repositories\LikeContract;
use App\Repositories\UserContract;
use App\Request\UpdateUserRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Http\Stream;
use Core\Support\Time;
use Core\Valid\Hash;
use DateTimeZone;

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

    public function rotate(UserContract $userContract): JsonResponse
    {
        $status = $userContract->generateNewAccessKey(Auth::id());

        if ($status === 1) {
            return $this->json->successStatusTrue();
        }

        return $this->json->errorServer();
    }

    public function user(): JsonResponse
    {
        return $this->json->successOK(Auth::user()->except(['id', 'password', 'is_admin', 'is_active', 'created_at', 'updated_at']));
    }

    public function configV2(): JsonResponse
    {
        return $this->json->successOK(Auth::user()->only(['tz', 'can_edit', 'can_delete', 'can_reply', 'tenor_key', 'is_confetti_animation']));
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $valid = $request->validated();

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        $user = Auth::user()->only(['id', 'password']);

        if (!empty($valid->name)) {
            $user->name = $valid->name;
        }

        if (!empty($valid->tz)) {
            if (!in_array($valid->tz, DateTimeZone::listIdentifiers())) {
                return $this->json->errorBadRequest(['Invalid time zone']);
            }

            $user->tz = $valid->tz;
        }

        if (array_key_exists('tenor_key', $request->all())) {
            $user->tenor_key = $valid->tenor_key;
        }

        if ($valid->get('filter') !== null) {
            $user->is_filter = boolval($valid->filter);
        }

        if ($valid->get('confetti_animation') !== null) {
            $user->is_confetti_animation = boolval($valid->confetti_animation);
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
            if (!Hash::check($valid->get('old_password'), $user->password ?? '')) {
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

    public function download(Stream $stream, CommentContract $comment): Stream
    {
        $streamResource = $stream->getStream();

        fputcsv($streamResource, [
            'uuid',
            'like',
            'name',
            'presence',
            'is_admin',
            'comment',
            'gif_url',
            'ip_address',
            'user_agent',
            'created_at',
            'parent_id',
        ]);

        foreach ($comment->downloadCommentByUserID(Auth::id()) as $value) {
            $value->insert_at = Time::factory($value->insert_at)->tz(auth()->user()->getTimezone());

            $data = array_map(function (mixed $value): mixed {
                if (is_bool($value)) {
                    return $value ? 'True' : 'False';
                }

                if (is_null($value)) {
                    return 'Null';
                }

                return $value;
            }, array_values(get_object_vars($value)));

            fputcsv($streamResource, $data);
        }

        return $stream->create(sprintf('backup_comments_%s.csv', now('y-m-d_H:i:s')))->download();
    }
}
