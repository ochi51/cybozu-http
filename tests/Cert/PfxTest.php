<?php

namespace CybozuHttp\Tests\Cert;

use PHPUnit\Framework\TestCase;
use CybozuHttp\Cert\Pfx;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class PfxTest extends TestCase
{

    public function testToPem(): void
    {
        $dir = __DIR__ . '/../_data/';
        try {
            Pfx::toPem($dir . 'test.pfx', 'fgq7n03e');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            self::fail('ERROR!! ' . get_class($e) . ' : ' . $e->getMessage());
        }

        try {
            Pfx::toPem($dir . 'not-exist.pfx', 'test');
            self::fail('Not throw Failed load cert file exception!');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            Pfx::toPem($dir . 'test.pfx', 'test');
            self::fail('Not throw Invalid cert password exception!');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
