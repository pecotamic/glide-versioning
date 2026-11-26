<?php

namespace Pecotamic\GlideVersioning\Tests;

use Statamic\Facades\Image;

/**
 * Without a published config the addon runs on its own defaults, which read
 * the environment. They have to be in place before the hash is registered.
 */
class EnvironmentTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('PECOTAMIC_GLIDE_VERSIONING_ENABLED=false');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('PECOTAMIC_GLIDE_VERSIONING_ENABLED');
    }

    public function test_versioning_can_be_disabled_through_the_environment()
    {
        $asset = $this->makeAsset('photo.jpg');
        $before = Image::manipulate($asset, ['w' => 20]);

        $this->reupload($asset);

        $this->assertSame($before, Image::manipulate($asset, ['w' => 20]));
    }
}
