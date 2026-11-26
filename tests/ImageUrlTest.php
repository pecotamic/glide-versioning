<?php

namespace Pecotamic\GlideVersioning\Tests;

use Orchestra\Testbench\Attributes\DefineEnvironment;
use Statamic\Facades\Image;
use Statamic\Support\Str;

class ImageUrlTest extends TestCase
{
    protected function disableVersioning($app): void
    {
        $app['config']->set('pecotamic.glide-versioning.enabled', false);
    }

    public function test_the_url_stays_the_same_while_the_asset_is_unchanged()
    {
        $asset = $this->makeAsset('photo.jpg');

        $this->assertSame(
            Image::manipulate($asset, ['w' => 20]),
            Image::manipulate($asset, ['w' => 20]),
        );
    }

    public function test_the_url_changes_when_the_asset_is_reuploaded()
    {
        $asset = $this->makeAsset('photo.jpg');
        $before = Image::manipulate($asset, ['w' => 20]);

        $this->reupload($asset);

        $this->assertNotSame($before, Image::manipulate($asset, ['w' => 20]));
    }

    public function test_the_generated_image_is_stored_under_the_versioned_url()
    {
        $asset = $this->makeAsset('photo.jpg');

        $url = Image::manipulate($asset, ['w' => 20]);

        $this->assertFileExists($this->glideCachePath.'/'.Str::after($url, '/img/'));
    }

    #[DefineEnvironment('disableVersioning')]
    public function test_the_url_does_not_change_when_versioning_is_disabled()
    {
        $asset = $this->makeAsset('photo.jpg');
        $before = Image::manipulate($asset, ['w' => 20]);

        $this->reupload($asset);

        $this->assertSame($before, Image::manipulate($asset, ['w' => 20]));
    }
}
