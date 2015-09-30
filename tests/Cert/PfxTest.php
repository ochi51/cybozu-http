<?php

namespace Cert;

use CybozuHttp\Cert\Pfx;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class PfxTest extends \PHPUnit_Framework_TestCase
{

    public function testToPem()
    {
        $dir = __DIR__ . '/../_data/';
        try {
            Pfx::toPem($dir . 'test.pfx', 'fgq7n03e');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail("ERROR!! " . get_class($e) . " : " . $e->getMessage());
        }

        try {
            Pfx::toPem($dir . 'not-exist.pfx', 'test');
            $this->fail("Not throw Failed load cert file exception!");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            Pfx::toPem($dir . 'test.pfx', 'test');
            $this->fail("Not throw Invalid cert password exception!");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
