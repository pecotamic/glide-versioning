<?php

namespace Pecotamic\GlideVersioning\Tests;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Statamic\Facades\StaticCache;

class FlushStaticCacheTest extends TestCase
{
    protected function disableVersioning($app): void
    {
        $app['config']->set('pecotamic.glide-versioning.enabled', false);
    }

    protected function disableFlushing($app): void
    {
        $app['config']->set('pecotamic.glide-versioning.flush_static_cache', false);
    }

    public function test_reuploading_an_asset_flushes_the_static_cache()
    {
        $asset = $this->makeAsset('photo.jpg');

        StaticCache::shouldReceive('flush')->atLeast()->once();

        $this->reupload($asset);
    }

    public function test_saving_an_asset_flushes_the_static_cache()
    {
        $asset = $this->makeAsset('photo.jpg');

        StaticCache::shouldReceive('flush')->once();

        $asset->set('alt', 'A photo')->save();
    }

    public function test_deleting_an_asset_flushes_the_static_cache()
    {
        $asset = $this->makeAsset('photo.jpg');

        StaticCache::shouldReceive('flush')->once();

        $asset->delete();
    }

    #[DefineEnvironment('disableFlushing')]
    public function test_the_static_cache_is_kept_when_flushing_is_disabled()
    {
        $asset = $this->makeAsset('photo.jpg');

        StaticCache::shouldReceive('flush')->never();

        $this->reupload($asset);
        $asset->delete();
    }

    #[DefineEnvironment('disableVersioning')]
    public function test_the_static_cache_is_kept_when_versioning_is_disabled()
    {
        $asset = $this->makeAsset('photo.jpg');

        StaticCache::shouldReceive('flush')->never();

        $this->reupload($asset);
        $asset->delete();
    }
}
