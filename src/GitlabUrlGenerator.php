<?php

namespace Zhanang19\MediaLibraryGitlab;

use DateTimeInterface;
use Illuminate\Support\Facades\Config;
use LogicException;
use Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined;
use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

class GitlabUrlGenerator extends BaseUrlGenerator
{
    /**
     * The config repository instance.
     *
     * @var \Zhanang19\MediaLibraryGitlab\Client
     */
    protected $client;

    /**
     * Create a new instance of GitlabUrlGenerator.
     */
    public function __construct()
    {
        $this->client = new Client(
            Config::get('filesystems.disks.gitlab.access_token'),
            Config::get('filesystems.disks.gitlab.project_id'),
            Config::get('filesystems.disks.gitlab.branch'),
            Config::get('filesystems.disks.gitlab.base_url')
        );
    }

    /**
     * Get the url for a media item.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getRawUrl($this->getPathRelativeToRoot());
    }

    /**
     * Get the temporary url for a media item.
     *
     * @param DateTimeInterface $expiration
     * @param array $options
     * @throws \Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        throw UrlCannotBeDetermined::filesystemDoesNotSupportTemporaryUrls();
    }

    /**
     * Get the url to the directory containing responsive images.
     *
     * @throws LogicException
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        throw new LogicException('Failed to create responsive images. Gitlab API does not support reading a file into a stream.');
    }

    /**
     * Get the repository url.
     *
     * @throws \Exception
     * @return string
     */
    protected function getRepositoryUrl(): string
    {
        try {
            return Config::get('filesystems.disks.gitlab.base_url') . '/' . $this->client->getPathNamespace();
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch repository path namespace from Gitlab API');
        }
    }

    /**
     * Get the raw file url.
     *
     * @param mixed $path
     */
    protected function getRawUrl($path): string
    {
        return $this->getRepositoryUrl() . '/-/raw/' . Config::get('filesystems.disks.gitlab.branch') . '/' . $path;
    }
}
