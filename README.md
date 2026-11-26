# Cache busting for statically cached Glide images

![Statamic 5.0+](https://img.shields.io/badge/Statamic-5.0+-FF269E?style=for-the-badge&link=https://statamic.com)
![Statamic 6.0+](https://img.shields.io/badge/Statamic-6.0+-FF269E?style=for-the-badge&link=https://statamic.com)

Pecotamic Glide Versioning is a Statamic addon which gives Glide images a new URL whenever the
underlying asset changes. Images can then be cached forever (`immutable`) by browsers and CDNs,
and replaced images still show up immediately.

## The problem

With static Glide caching (`'cache' => true` or `'cache' => 'hybrid'`), the file name of a
manipulated image is derived from the source file and the manipulation parameters. When an
asset is replaced with a file of the same name (e.g. re-cropped in the control panel), the URL
stays the same, and browsers and CDNs keep serving the old image.

## What it does

- Adds the asset's last modified time to the Glide cache hash, so every change results in a
  new URL. Works with `'cache' => true` and `'cache' => 'hybrid'` (Statamic 6.34+).
- Flushes the static page cache when an asset is reuploaded, saved or deleted, so cached pages
  are rendered with the new image URL.

Images from paths or remote URLs are not versioned and keep Statamic's default hash.

## How to Install

``` bash
composer require pecotamic/glide-versioning
```

The package requires PHP 8.2+. It will auto register.

Enable static Glide caching in `config/statamic/assets.php`:

```php
'image_manipulation' => [
    'route' => 'img',
    'cache' => 'hybrid', // or true
    'cache_path' => public_path('img'),
],
```

Add the generated images to `.gitignore`:

```
/public/img
```

Then clear the caches once:

```sh
php please glide:clear
php please static:clear
```

## Web server

The generated images are served directly by the web server. As the URL changes with every
change of the asset, they can be cached forever. Nginx:

```nginx
location ^~ /img/ {
    try_files $uri /index.php?$args;
    add_header Cache-Control "public, max-age=31536000, immutable";
}
```

**Only use this rule together with this addon.** Without versioning, replaced images would
stay in the browser cache for a year. With `'cache' => false` the rule has no effect, because
the requests are handled by PHP and its headers apply.

## Configuration (optional)

You can override the default options by publishing the configuration:

```sh
php artisan vendor:publish --tag=pecotamic-glide-versioning-config
```

This will copy the default config file to `config/pecotamic/glide-versioning.php`.

```env
PECOTAMIC_GLIDE_VERSIONING_ENABLED=true
PECOTAMIC_GLIDE_VERSIONING_FLUSH_STATIC_CACHE=true
```

## Notes

- In hybrid mode Statamic stores the mapping between image URLs and source images in the Glide
  cache store. Don't clear it on deploy: `php artisan cache:clear` keeps it,
  `php please glide:clear` removes it.
- Switching `'cache'` from `false` to `true` or `'hybrid'` changes all image URLs. Clear the
  static cache afterwards.
