<?php

namespace CybozuHttp\Api\User;

use CybozuHttp\Client;
use CybozuHttp\Api\UserApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserOrganizations
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Csv
     */
    private $csv;

    public function __construct(Client $client, Csv $csv)
    {
        $this->client = $client;
        $this->csv = $csv;
    }

    /**
     * Get organizations and titles of user
     * https://cybozudev.zendesk.com/hc/ja/articles/202124774#step2
     *
     * @param string $code
     * @return array
     */
    public function get($code): array
    {
        $options = ['json' => ['code' => $code]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(UserApi::generateUrl('user/organizations.json'), $options)
            ->getBody();

        return $stream->jsonSerialize()['organizationTitles'];
    }

    /**
     * Get userOrganizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124774#step1
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('userOrganizations');
    }

    /**
     * Post userOrganizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202362860
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('userOrganizations', $filename);
    }
}
