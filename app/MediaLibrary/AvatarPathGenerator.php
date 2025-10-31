<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class AvatarPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive/';
    }

    /**
     * Get the base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        // Get the model ID (user ID in this case)
        $modelId = $media->model_id;

        // Get the collection name
        $collectionName = $media->collection_name;

        // Return path: {model_id}/{collection_name}
        // Example: 1/avatar
        return $modelId.'/'.$collectionName;
    }
}
