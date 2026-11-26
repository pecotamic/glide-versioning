<?php

namespace Pecotamic\GlideVersioning\Listeners;

use Statamic\Facades\StaticCache;

class FlushStaticCache
{
    /**
     * Cached pages still reference the previous image URL after an asset has
     * changed, so flush the static cache to render them with the new URL.
     */
    public function handle(): void
    {
        if (! config('pecotamic.glide-versioning.flush_static_cache', true)) {
            return;
        }

        StaticCache::flush();
    }
}
