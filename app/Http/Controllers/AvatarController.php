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

        // Try to get the thumb conversion, fallback to original if it doesn't exist
        $thumbPath = $media->getPath('thumb');
        $path = file_exists($thumbPath) ? $thumbPath : $media->getPath();

        return response()->file($path, [
            'Content-Type' => $media->mime_type,
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
