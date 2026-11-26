<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Glide Versioning
    |--------------------------------------------------------------------------
    |
    | Adds the asset's last modified time to the Glide cache hash, so the
    | image URL changes whenever an asset is replaced. Only has an effect
    | when Glide images are cached statically ('cache' => true or 'hybrid'
    | in config/statamic/assets.php).
    |
    */
    'enabled' => env('PECOTAMIC_GLIDE_VERSIONING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Flush Static Cache
    |--------------------------------------------------------------------------
    |
    | Cached pages still reference the previous image URL after an asset
    | has changed. When enabled, the static page cache is flushed whenever
    | an asset is reuploaded, saved or deleted.
    |
    */
    'flush_static_cache' => env('PECOTAMIC_GLIDE_VERSIONING_FLUSH_STATIC_CACHE', true),
];
