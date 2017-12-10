<?php

namespace Api\Kintone;

require_once __DIR__ . '/../../_support/KintoneTestHelper.php';
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CommentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KintoneApi
     */
    private $api;

    /**
     * @var integer
     */
    private $spaceId;

    /**
     * @var array
     */
    private $space;

    /**
     * @var integer
     */
    private $appId;

    protected function setup()
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $this->space = $this->api->space()->get($this->spaceId);
        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $this->space['defaultThread']);
    }

    public function testComments()
    {
        $recordIds = [];
        $comments = [];
        for ($i = 0; $i <= 10; $i++) {
            $id = $this->api->record()->post($this->appId, KintoneTestHelper::getRecord())['id'];
            $recordIds[] = $id;
            $comments[$id] = [];
            for ($j = 0; $j <= 10; $j++) {
                $comments[$id][] = ['text' => 'test comment' . $j];
            }
        }
        $this->api->comments()->postByRecords($this->appId, $comments);

        $result = $this->api->comments()->allByRecords($this->appId, $recordIds);
        $n = 0;
        foreach ($result as $recordId => $commentsResult) {
            self::assertEquals($recordId, $recordIds[$n]);
            $m = 0;
            foreach ($commentsResult as $comment) {
                self::assertEquals(rtrim(ltrim($comment['text'])), 'test comment' . $m);
                $m++;
            }
            $n++;
        }
    }

    protected function tearDown()
    {
        $this->api->space()->delete($this->spaceId);
    }
}
