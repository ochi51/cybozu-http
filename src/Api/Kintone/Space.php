<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;
use CybozuHttp\Middleware\JsonStream;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Space
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get space
     * https://cybozudev.zendesk.com/hc/ja/articles/202166200
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function get($id, $guestSpaceId = null): array
    {
        $options = ['json' => ['id' => $id]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('space.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Post space
     * https://cybozudev.zendesk.com/hc/ja/articles/202166210#step1
     *
     * @param integer $id
     * @param string  $name
     * @param array   $members
     * @param boolean $isPrivate
     * @param boolean $isGuest
     * @param boolean $fixedMember
     * @return array
     */
    public function post($id, $name, array $members, $isPrivate = false, $isGuest = false, $fixedMember = false): array
    {
        $options = ['json' => compact('id', 'name', 'members', 'isPrivate', 'isGuest', 'fixedMember')];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->post(KintoneApi::generateUrl('template/space.json'), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Delete space
     * https://cybozudev.zendesk.com/hc/ja/articles/202166250
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function delete($id, $guestSpaceId = null): array
    {
        $options = ['json' => ['id' => $id]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->delete(KintoneApi::generateUrl('space.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Put space body
     * https://cybozudev.zendesk.com/hc/ja/articles/201941884
     *
     * @param integer $id
     * @param string  $body
     * @param integer $guestSpaceId
     * @return array
     */
    public function putBody($id, $body, $guestSpaceId = null): array
    {
        $options = ['json' => compact('id', 'body')];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->put(KintoneApi::generateUrl('space/body.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Get space members
     * https://cybozudev.zendesk.com/hc/ja/articles/202166220
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function getMembers($id, $guestSpaceId = null): array
    {
        $options = ['json' => ['id' => $id]];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->get(KintoneApi::generateUrl('space/members.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Put space members
     * https://cybozudev.zendesk.com/hc/ja/articles/202166230
     *
     * @param integer $id
     * @param array   $members
     * @param integer $guestSpaceId
     * @return array
     */
    public function putMembers($id, array $members, $guestSpaceId = null): array
    {
        $options = ['json' => compact('id', 'members')];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->put(KintoneApi::generateUrl('space/members.json', $guestSpaceId), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }

    /**
     * Put space guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/201941904
     *
     * @param integer $id
     * @param array   $guests
     * @return array
     */
    public function putGuests($id, array $guests): array
    {
        $options = ['json' => compact('id', 'guests')];

        /** @var JsonStream $stream */
        $stream = $this->client
            ->put(KintoneApi::generateUrl('space/guests.json', $id), $options)
            ->getBody();

        return $stream->jsonSerialize();
    }
}
