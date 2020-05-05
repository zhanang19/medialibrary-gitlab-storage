# Spatie Medialibrary Gitlab Storage
This package provide an URL Generator for assets that stored in Gitlab Storage.

## Installation
Run command `composer require zhanang19/medialibrary-gitlab-storage`
> This package is tested only in Laravel 7

## Configuration
1. This package require `spatie/medialibrary:^7.0` by default. So, you can remove `spatie/medialibrary` from your `composer.json`. Configure it to use gitlab as media disk in your `.env` file
```
MEDIA_DISK="gitlab"
```

2. This package require `zhanang19/laravel-gitlab-storage` by default. Just initiate config for that
```php
    # config/filesystems.php
    'disks' => [
        'gitlab' => [
            'driver' => 'gitlab',
            'project_id' => env('GITLAB_PROJECT_ID'),
            'access_token' => env('GITLAB_ACCESS_TOKEN', ''),
            'branch' => env('GITLAB_BRANCH', 'master'),
            'base_url' => env('GITLAB_BASE_URL', 'https://gitlab.com'),
            'debug' => (bool)env('GITLAB_DEBUG', env('APP_DEBUG', false))
        ]
    ],
```

3. Configure custom url generator in `medialibrary.php` config

```php
    # config/medialibrary.php
    'url_generator' => \Zhanang19\MediaLibraryGitlab\GitlabUrlGenerator::class,
```

## Caveat
Conversion and responsive images doesn't working correctly. This cause by Gitlab API doesn't support streaming file to copy and process the image. 

## Reference
1. [Generating custom urls](https://docs.spatie.be/laravel-medialibrary/v7/advanced-usage/generating-custom-urls/)