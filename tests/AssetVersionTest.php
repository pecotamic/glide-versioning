<?php

namespace Pecotamic\GlideVersioning\Tests;

use Pecotamic\GlideVersioning\AssetVersion;

class AssetVersionTest extends TestCase
{
    public function test_an_asset_in_the_container_root_is_versioned_by_its_last_modified_time()
    {
        $asset = $this->makeAsset('photo.jpg');

        $this->assertSame((string) $asset->meta('last_modified'), AssetVersion::for('containers/main/.', 'photo.jpg'));
        $this->assertSame((string) $asset->meta('last_modified'), AssetVersion::for('containers/main', 'photo.jpg'));
    }

    public function test_an_asset_in_a_folder_is_versioned_by_its_last_modified_time()
    {
        $asset = $this->makeAsset('gallery/summer/photo.jpg');

        $this->assertSame((string) $asset->meta('last_modified'), AssetVersion::for('containers/main/gallery/summer', 'photo.jpg'));
    }

    public function test_sources_other_than_assets_have_no_version()
    {
        $this->makeAsset('photo.jpg');

        $this->assertNull(AssetVersion::for(null, 'photo.jpg'));
        $this->assertNull(AssetVersion::for('paths', 'photo.jpg'));
        $this->assertNull(AssetVersion::for('http', 'photo.jpg'));
    }

    public function test_a_missing_asset_has_no_version()
    {
        $this->assertNull(AssetVersion::for('containers/main', 'missing.jpg'));
    }
}
