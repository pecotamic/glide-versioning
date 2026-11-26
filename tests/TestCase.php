<?php

namespace Pecotamic\GlideVersioning\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Pecotamic\GlideVersioning\ServiceProvider;
use Statamic\Assets\ReplacementFile;
use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

abstract class TestCase extends AddonTestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected string $glideCachePath;

    protected function defineEnvironment($app)
    {
        $this->glideCachePath = sys_get_temp_dir().'/pecotamic-glide-versioning-'.uniqid();

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('statamic.assets.image_manipulation.cache', true);
        $app['config']->set('statamic.assets.image_manipulation.cache_path', $this->glideCachePath);

        // Statamic keeps the generated image paths in this store. On disk it
        // would outlive the test and hand out URLs of a previous one.
        $app['config']->set('cache.stores.glide', ['driver' => 'array']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('assets');
        Storage::fake('local');

        AssetContainer::make('main')->disk('assets')->save();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->glideCachePath);

        parent::tearDown();
    }

    /**
     * The file is dated back, as the last modified time has a resolution of
     * one second and a reupload in the same second would keep it unchanged.
     */
    protected function makeAsset(string $path): AssetContract
    {
        $disk = Storage::disk('assets');
        $disk->putFileAs(dirname($path), UploadedFile::fake()->image(basename($path), 40, 40), basename($path));
        touch($disk->path($path), time() - 60);

        $asset = Asset::make()->container('main')->path($path);
        $asset->save();

        return $asset;
    }

    protected function reupload(AssetContract $asset): void
    {
        Storage::disk('local')->putFileAs('', UploadedFile::fake()->image('replacement.jpg', 50, 50), 'replacement.jpg');

        $asset->reupload(new ReplacementFile('replacement.jpg'));
    }
}
