<?php

namespace Pecotamic\GlideVersioning;

use Statamic\Facades\Asset;

class AssetVersion
{
    /**
     * Returns the last modified time of the asset behind a Glide source file.
     *
     * Statamic sets the Glide cache path prefix to "containers/<container>/<folder>"
     * both when generating an image (ImageGenerator) and when resolving the URL in
     * hybrid mode (GlideCachePathResolver). Together with the file name this
     * identifies the asset, so rendering and generating always produce the same hash.
     * Non-asset sources (paths, remote URLs) have no version.
     */
    public static function for(?string $cachePathPrefix, string $source): ?string
    {
        if (! $cachePathPrefix || ! preg_match('#^containers/([^/]+)(?:/(.*))?$#', $cachePathPrefix, $matches)) {
            return null;
        }

        $folder = trim($matches[2] ?? '', '/');
        $path = ($folder === '' || $folder === '.') ? $source : $folder.'/'.$source;

        $modified = Asset::find($matches[1].'::'.$path)?->meta('last_modified');

        return $modified ? (string) $modified : null;
    }
}
