<?php

namespace Pecotamic\GlideVersioning\Tests;

use PHPUnit\Framework\Attributes\RequiresMethod;
use Statamic\Facades\Image;
use Statamic\Imaging\HybridUrlBuilder;
use Statamic\Support\Str;

/**
 * In hybrid mode the URL is rendered first and the image is generated on its
 * first request, so both have to arrive at the same hash.
 *
 * Hybrid caching was added in Statamic 6.34.
 */
#[RequiresMethod(HybridUrlBuilder::class, 'build')]
class HybridImageUrlTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('statamic.assets.image_manipulation.cache', 'hybrid');
    }

    public function test_the_image_is_generated_under_the_rendered_url()
    {
        $asset = $this->makeAsset('photo.jpg');
        $url = Image::manipulate($asset, ['w' => 20]);

        $this->get($url)->assertOk();

        $this->assertFileExists($this->glideCachePath.'/'.Str::after($url, '/img/'));
    }

    public function test_the_url_changes_when_the_asset_is_reuploaded()
    {
        $asset = $this->makeAsset('photo.jpg');
        $before = Image::manipulate($asset, ['w' => 20]);

        $this->reupload($asset);
        $after = Image::manipulate($asset, ['w' => 20]);

        $this->assertNotSame($before, $after);
        $this->get($after)->assertOk();
    }
}
