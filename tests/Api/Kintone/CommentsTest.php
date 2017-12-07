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
        $commentIds = [];
        for ($i = 0; $i <= 10; $i++) {
            $id = $this->api->record()->post($this->appId, KintoneTestHelper::getRecord())['id'];
            $recordIds[] = $id;

        }
        for ($j = 0; $j <= 10; $j++) {
            $commentIds[] = $this->api->comment()->post($this->appId, $recordIds[0], 'test comment' . $j)['id'];
        }
        for ($j = 11; $j <= 21; $j++) {
            $commentIds[] = $this->api->comment()->post($this->appId, $recordIds[1], 'test comment' . $j)['id'];
        }

        $result = $this->api->comments()->allByRecords($this->appId, $recordIds);
        $n = $m = 0;
        foreach ($result as $recordId => $comments) {
            self::assertEquals($recordId, $recordIds[$n]);
            foreach ($comments as $comment) {
                self::assertEquals($comment['id'], $commentIds[$m]);
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
