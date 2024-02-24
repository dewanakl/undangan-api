<?php

namespace App\Controllers\Api;

use App\Repositories\CommentContract;
use App\Repositories\LikeContract;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Request;

class DashboardController extends Controller
{
    private $json;

    public function __construct(JsonResponse $json)
    {
        $this->json = $json;
    }

    function stats(CommentContract $comment, LikeContract $like)
    {
        $likes = $like->countLikeByUserID(Auth::id());
        $comments = $comment->countPresenceByUserID(Auth::id());

        $present = 0;
        $absent = 0;
        foreach ($comments as $presence) {
            if ($presence->hadir) {
                $present++;
            } else {
                $absent++;
            }
        }

        return $this->json->successOK([
            'stats' => [
                'present' => $present,
                'absent' => $absent,
                'likes' => $likes
            ]
        ]);
    }
}
