<?php

namespace Zhanang19\MediaLibraryGitlab;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;

class Client
{
    const VERSION_URI = '/api/v4';

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * Client constructor.
     *
     * @param string $accessToken
     * @param string $projectId
     * @param string $branch
     * @param string $baseUrl
     */
    public function __construct(string $accessToken, string $projectId, string $branch, string $baseUrl)
    {
        $this->accessToken = $accessToken;
        $this->projectId = $projectId;
        $this->branch = $branch;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get path with namespace.
     */
    public function getPathNamespace()
    {
        $response = $this->request('GET', $this->projectId);
        $result = $this->parse($response);

        return $result['path_with_namespace'];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \GuzzleHttp\Psr7\Response
     */
    private function request(string $method, string $uri, array $params = []): Response
    {
        $uri = ($method === 'GET') ? $this->buildUri($uri, $params) : $this->buildUri($uri);

        $params = ($method !== 'GET') ? ['form_params' => array_merge(['branch' => $this->branch], $params)] : [];

        $client = new HttpClient(['headers' => ['PRIVATE-TOKEN' => $this->accessToken]]);

        return $client->request($method, $uri, $params);
    }

    /**
     * @param string $uri
     * @param $params
     *
     * @return string
     */
    private function buildUri(string $uri, array $params = []): string
    {
        $params = array_merge(['ref' => $this->branch], $params);

        $params = array_map('urlencode', $params);

        if (isset($params['path'])) {
            $params['path'] = urldecode($params['path']);
        }

        $params = http_build_query($params);

        $params = !empty($params) ? "?$params" : null;

        $baseUrl = rtrim($this->baseUrl, '/') . self::VERSION_URI;

        return "{$baseUrl}/projects/{$uri}";
    }

    /**
     * Parse Guzzle Response.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @param bool $json
     * @return mixed|string
     */
    private function parse(Response $response, $json = true)
    {
        $contents = $response->getBody()
            ->getContents();

        return ($json) ? json_decode($contents, true) : $contents;
    }
}
