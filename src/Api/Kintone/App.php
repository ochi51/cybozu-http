<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class App
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
     * Get app
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function get($id, $guestSpaceId = null)
    {
        $options = ['json' => ['id' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('app.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app settings
     * https://cybozudev.zendesk.com/hc/ja/articles/204694170
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getSettings($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/settings.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app forms
     * https://cybozudev.zendesk.com/hc/ja/articles/201941834#step1
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function getForm($id, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('form.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize()['properties'];
    }

    /**
     * Get app form fields
     * https://cybozudev.zendesk.com/hc/ja/articles/204783170#anchor_getform_fields
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getFields($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/form/fields.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app form layout
     * https://cybozudev.zendesk.com/hc/ja/articles/204783170#anchor_getform_layout
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getLayout($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/form/layout.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app views
     * https://cybozudev.zendesk.com/hc/ja/articles/204529784
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getViews($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/views.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app acl
     * https://cybozudev.zendesk.com/hc/ja/articles/204529754
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getAcl($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get record acl
     * https://cybozudev.zendesk.com/hc/ja/articles/204791510
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getRecordAcl($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('record/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get field acl
     * https://cybozudev.zendesk.com/hc/ja/articles/204791520
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param string $lang
     * @return array
     */
    public function getFieldAcl($id, $guestSpaceId = null, $lang = 'default')
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('field/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app JavaScript and CSS customize
     * https://cybozudev.zendesk.com/hc/ja/articles/204529824
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function getCustomize($id, $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $id]];

        return $this->client
            ->get(KintoneApi::generateUrl('app/customize.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }
}