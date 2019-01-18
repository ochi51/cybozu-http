<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class OrganizationUsers
{
    public const MAX_GET_USERS = 100;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get users and titles of organization
     * https://cybozudev.zendesk.com/hc/ja/articles/202124774#step2
     *
     * @param string $code
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get($code, $offset = 0, $limit = self::MAX_GET_USERS): array
    {
        $options = ['json' => [
            'code' => $code,
            'offset' => $offset,
            'size' => $limit
        ]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(UserApi::generateUrl('organization/users.json'), $options)
            ->getBody();

        return $stream->jsonSerialize()['userTitles'];
    }
}
