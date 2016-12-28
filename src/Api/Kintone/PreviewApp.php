<?php

namespace CybozuHttp\Api\Kintone;

use CybozuHttp\Client;
use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class PreviewApp
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
     * Deploy app
     * https://cybozudev.zendesk.com/hc/ja/articles/204699420
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @param integer $revision
     * @param boolean $revert
     * @return array
     */
    public function deploy($id, $guestSpaceId = null, $revision = -1, $revert = false)
    {
        return $this->deployApps([[
            'app' => $id,
            'revision' => $revision
        ]], $guestSpaceId, $revert);
    }

    /**
     * Deploy apps
     * https://cybozudev.zendesk.com/hc/ja/articles/204699420
     *
     * @param array   $apps
     * @param integer $guestSpaceId
     * @param boolean $revert
     * @return array
     */
    public function deployApps(array $apps, $guestSpaceId = null, $revert = false)
    {
        $options = ['json' => [
            'apps' => $apps,
            'revert' => $revert
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('preview/app/deploy.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * get deploy status
     * https://cybozudev.zendesk.com/hc/ja/articles/204699420
     *
     * @param integer $id
     * @param integer $guestSpaceId
     * @return array
     */
    public function getDeployStatus($id, $guestSpaceId = null)
    {
        return $this->getDeployStatuses([$id], $guestSpaceId)[0];
    }

    /**
     * get deploy statuses
     * https://cybozudev.zendesk.com/hc/ja/articles/204699420
     *
     * @param array $ids
     * @param integer $guestSpaceId
     * @return array
     */
    public function getDeployStatuses(array $ids, $guestSpaceId = null)
    {
        $options = ['json' => ['apps' => $ids]];

        return $this->client
            ->get(KintoneApi::generateUrl('preview/app/deploy.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize()['apps'];
    }

    /**
     * Post preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/202931674#step1
     *
     * @param string  $name
     * @param integer $spaceId
     * @param integer $threadId
     * @param integer $guestSpaceId
     * @return array
     */
    public function post($name, $spaceId = null, $threadId = null, $guestSpaceId = null)
    {
        $options = ['json' => ['name' => $name]];
        if ($spaceId !== null) {
            $options['json']['space'] = $spaceId;
            $options['json']['thread'] = $threadId;
        }

        return $this->client
            ->post(KintoneApi::generateUrl('preview/app.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/app/settings.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
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
            ->get(KintoneApi::generateUrl('preview/app/form/fields.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/app/form/layout.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/app/views.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/app/acl.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/record/acl.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/field/acl.json', $guestSpaceId), $options)
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
            ->get(KintoneApi::generateUrl('preview/app/customize.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Get app status list
     * https://cybozudev.zendesk.com/hc/ja/articles/216972946
     *
     * @param integer $id
     * @param string  $lang
     * @param integer $guestSpaceId
     * @return array
     */
    public function getStatus($id, $lang = 'ja', $guestSpaceId = null)
    {
        $options = ['json' => ['app' => $id, 'lang' => $lang]];

        return $this->client
            ->get(KintoneApi::generateUrl('preview/app/status.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put preview app settings
     * https://cybozudev.zendesk.com/hc/ja/articles/204730520
     *
     * @param integer $id
     * @param string  $name
     * @param string  $description
     * @param array   $icon
     * @param string  $theme
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putSettings($id, $name, $description, array $icon, $theme, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'name' => $name,
            'description' => $description,
            'icon' => $icon,
            'theme' => $theme,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/settings.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Post form fields to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529724#anchor_changeform_addfields
     *
     * @param integer $id
     * @param array   $fields
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function postFields($id, array $fields, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'properties' => $fields,
            'revision' => $revision
        ]];

        return $this->client
            ->post(KintoneApi::generateUrl('preview/app/form/fields.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put form fields to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529724#anchor_changeform_changefields
     *
     * @param integer $id
     * @param array   $fields
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putFields($id, array $fields, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'properties' => $fields,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/form/fields.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Delete form fields to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529724#anchor_changeform_deletefields
     *
     * @param integer $id
     * @param array   $fields
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function deleteFields($id, array $fields, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'fields' => $fields,
            'revision' => $revision
        ]];

        return $this->client
            ->delete(KintoneApi::generateUrl('preview/app/form/fields.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put form layout to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529724#anchor_changeform_changelayout
     *
     * @param integer $id
     * @param array   $layout
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putLayout($id, array $layout, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'layout' => $layout,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/form/layout.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put views to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529794
     *
     * @param integer $id
     * @param array   $views
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putViews($id, array $views, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'views' => $views,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/views.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put app acl to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/201941844
     *
     * @param integer $id
     * @param array   $rights
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putAcl($id, array $rights, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'rights' => $rights,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put record acl to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/201941854
     *
     * @param integer $id
     * @param array   $rights
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putRecordAcl($id, array $rights, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'id' => $id,
            'rights' => $rights,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/record/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put field acl to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/201941854
     *
     * @param integer $id
     * @param array   $rights
     * @param integer $guestSpaceId
     * @param integer $revision
     * @return array
     */
    public function putFieldAcl($id, array $rights, $guestSpaceId = null, $revision = -1)
    {
        $options = ['json' => [
            'id' => $id,
            'rights' => $rights,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/field/acl.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put app JavaScript & CSS customize to preview app
     * https://cybozudev.zendesk.com/hc/ja/articles/204529834
     *
     * @param integer $id
     * @param array   $js
     * @param array   $css
     * @param array   $mobileJs
     * @param integer $guestSpaceId
     * @param string  $scope
     * @param integer $revision
     * @return array
     */
    public function putCustomize($id, array $js = [], array $css = [], array $mobileJs = [], $guestSpaceId = null, $scope = 'ALL', $revision = -1)
    {
        $options = ['json' => [
            'app' => $id,
            'desktop' => [
                'js' => $js,
                'css' => $css,
            ],
            'mobile' => [
                'js' => $mobileJs
            ],
            'scope' => $scope,
            'revision' => $revision
        ]];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/customize.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }

    /**
     * Put app status and action
     * https://cybozudev.zendesk.com/hc/ja/articles/217905503
     *
     * @param integer $id
     * @param array   $states
     * @param array   $actions
     * @param boolean $enable
     * @param integer $guestSpaceId
     * @return array
     */
    public function putStatus($id, array $states, array $actions, $enable = true, $guestSpaceId = null)
    {
        $options = [
            'json' => [
                'app' => $id,
                'enable' => $enable,
                'states' => $states,
                'actions' => $actions
            ]
        ];

        return $this->client
            ->put(KintoneApi::generateUrl('preview/app/status.json', $guestSpaceId), $options)
            ->getBody()->jsonSerialize();
    }
}