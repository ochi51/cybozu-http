<?php

namespace CybozuHttp\Api\User;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class UserGroups
{
    /**
     * @var Csv
     */
    private $csv;

    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get userGroups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124784
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('userGroups');
    }

    /**
     * Post userGroups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202362890
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('userGroups', $filename);
    }
}
