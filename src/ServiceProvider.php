<?php

namespace Pecotamic\GlideVersioning;

use League\Glide\Server;
use Pecotamic\GlideVersioning\Listeners\FlushStaticCache;
use Statamic\Events\AssetDeleted;
use Statamic\Events\AssetReuploaded;
use Statamic\Events\AssetSaved;
use Statamic\Facades\Glide;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        AssetReuploaded::class => [FlushStaticCache::class],
        AssetSaved::class => [FlushStaticCache::class],
        AssetDeleted::class => [FlushStaticCache::class],
    ];

    public function register(): void
    {
        parent::register();

        // Statamic captures the hash callback when the Glide server is created,
        // which can happen before this provider boots. Register it while booting.
        $this->app->booting(function () {
            Glide::generateHashUsing(function (string $source, array $params) {
                $hash = $source.'?'.http_build_query($params);

                $version = AssetVersion::for(app(Server::class)->getCachePathPrefix(), $source);

                if ($version !== null) {
                    $hash .= '#'.$version;
                }

                return md5($hash);
            });
        });
    }

    protected function bootConfig(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/glide-versioning.php', 'pecotamic.glide-versioning');

        $this->publishes([
            __DIR__.'/../config/glide-versioning.php' => config_path('pecotamic/glide-versioning.php'),
        ], 'pecotamic-glide-versioning-config');

        return $this;
    }
}
