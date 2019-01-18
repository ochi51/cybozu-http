<?php

namespace CybozuHttp\Api\User;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserServices
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * UserServices constructor.
     * @param Csv $csv
     */
    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get userServices by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363070
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('userServices');
    }

    /**
     * Post userServices by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124734
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('userServices', $filename);
    }
}
