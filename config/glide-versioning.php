<?php

return [
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
