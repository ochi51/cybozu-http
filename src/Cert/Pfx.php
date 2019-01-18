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
     * @throws \RuntimeException
     */
    public static function toPem($pfx, $password): string
    {
        $p12cert = array();
        $p12buf = self::read($pfx);
        $p12cert = self::pkcs12Read($p12buf, $p12cert, $password);

        if (empty($p12cert['cert']) || empty($p12cert['pkey'])) {
            throw new \RuntimeException('Cert file not include info.');
        }

        $pem = $p12cert['cert'] . "\n" . $p12cert['pkey'] . "\n";

        return self::addExtracerts($pem, $p12cert);
    }

    /**
     * @param $pfx
     * @return string
     * @throws \RuntimeException
     */
    private static function read($pfx): string
    {
        try {
            $fd = fopen($pfx, 'rb');
            $p12buf = fread($fd, filesize($pfx));
            fclose($fd);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed load cert file.');
        }

        return $p12buf;
    }

    /**
     * @param $p12buf
     * @param $p12cert
     * @param $password
     * @return array
     * @throws \RuntimeException
     */
    private static function pkcs12Read($p12buf, array $p12cert, $password): array
    {
        if (!openssl_pkcs12_read($p12buf, $p12cert, $password)) {
            throw new \RuntimeException('Invalid cert format or password.');
        }

        return $p12cert;
    }

    /**
     * @param $pem
     * @param $p12cert
     * @return string
     */
    private static function addExtracerts($pem, $p12cert): string
    {
        if (!empty($p12cert['extracerts'][0])) {
            $pem .= $p12cert['extracerts'][0];
        }

        return $pem;
    }
}
