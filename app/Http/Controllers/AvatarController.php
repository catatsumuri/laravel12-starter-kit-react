<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AvatarController extends Controller
{
    /**
     * Display the specified user's avatar.
     */
    public function show(User $user): BinaryFileResponse|Response
    {
        $media = $user->getFirstMedia('avatar');

        if (! $media) {
            abort(404, 'Avatar not found');
        }

        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
