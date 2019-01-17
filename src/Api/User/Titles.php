<?php

namespace CybozuHttp\Api\User;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Titles
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * Titles constructor.
     * @param Csv $csv
     */
    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get titles by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124764
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('title');
    }

    /**
     * Post titles by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202361180
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('title', $filename);
    }
}
