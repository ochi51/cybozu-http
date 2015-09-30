<?php

namespace CybozuHttp\Cert;


/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Pfx
{
    /**
     * @param $pfx
     * @param $password
     * @return string
     * @throws \Exception
     */
    public static function toPem($pfx, $password)
    {
        $p12cert = array();

        try {
            $fd = fopen($pfx, 'r');
            $p12buf = fread($fd, filesize($pfx));
            fclose($fd);
        } catch (\Exception $e) {
            throw new \Exception("kintoneAccount.failed_load_cert");
        }

        if (!openssl_pkcs12_read($p12buf, $p12cert, $password)) {
            throw new \Exception("kintoneAccount.failed_cert_format");
        }

        if (empty($p12cert["cert"]) || empty($p12cert["pkey"])) {
            throw new \Exception("kintoneAccount.cert_not_include_info");
        }

        $pem = $p12cert["cert"] . "\n" . $p12cert["pkey"] . "\n";

        if (!empty($p12cert["extracerts"][0])) {
            $pem = $pem . $p12cert["extracerts"][0];
        }

        return $pem;
    }
}