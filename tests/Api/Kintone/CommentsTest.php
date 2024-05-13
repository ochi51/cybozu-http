<?php

namespace CybozuHttp\Tests\Api\Kintone;

use PHPUnit\Framework\TestCase;
use KintoneTestHelper;

use CybozuHttp\Api\KintoneApi;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class CommentsTest extends TestCase
{
    /**
     * @var KintoneApi
     */
    private KintoneApi $api;

    /**
     * @var int
     */
    private int $spaceId;

    /**
     * @var int
     */
    private int $appId;

    protected function setup(): void
    {
        $this->api = KintoneTestHelper::getKintoneApi();
        $this->spaceId = KintoneTestHelper::createTestSpace();
        $space = $this->api->space()->get($this->spaceId);
        $this->appId = KintoneTestHelper::createTestApp($this->spaceId, $space['defaultThread']);
    }

    public function testComments(): void
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
            $this->assertEquals($recordId, $recordIds[$n]);
            $m = 0;
            foreach ($commentsResult as $comment) {
                $this->assertEquals(rtrim(ltrim($comment['text'])), 'test comment' . $m);
                $m++;
            }
            $n++;
        }
    }

    protected function tearDown(): void
    {
        $this->api->space()->delete($this->spaceId);
    }
}
