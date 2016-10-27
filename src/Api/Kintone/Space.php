<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

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
    public function get($id, $guestSpaceId = null)
    {
        $options = ['json' => ['id' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('space.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
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
    public function post($id, $name, array $members, $isPrivate = false, $isGuest = false, $fixedMember = false)
    {
        $options = ['json' => [
            'id' => $id,
            'name' => $name,
            'members' => $members,
            'isPrivate' => $isPrivate,
            'isGuest' => $isGuest,
            'fixedMember' => $fixedMember,
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('template/space.json'), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Delete space
     * https://cybozudev.zendesk.com/hc/ja/articles/202166250
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function delete($id, $guestSpaceId = null)
    {
        $options = ['json' => ['id' => $id]];

        return $this->client
            ->delete(KintoneApi::generateUrl('space.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
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
    public function putBody($id, $body, $guestSpaceId = null)
    {
        $options = ['json' => [
            'id' => $id,
            'body' => $body
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('space/body.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get space members
     * https://cybozudev.zendesk.com/hc/ja/articles/202166220
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function getMembers($id, $guestSpaceId = null)
    {
        $options = ['json' => ['id' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('space/members.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
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
    public function putMembers($id, array $members, $guestSpaceId = null)
    {
        $options = ['json' => [
            'id' => $id,
            'members' => $members
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('space/members.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put space guest users
     * https://cybozudev.zendesk.com/hc/ja/articles/201941904
     *
     * @param integer $id
     * @param array   $guests
     * @return array
     */
    public function putGuests($id, array $guests)
    {
        $options = ['json' => [
            'id' => $id,
            'guests' => $guests
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('space/guests.json', $id), $options)
            ->getBody()->jsonSerialize();
    }
}