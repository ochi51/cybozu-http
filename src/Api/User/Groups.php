<?php

namespace CybozuHttp\Api\User;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Groups
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * Groups constructor.
     * @param Csv $csv
     */
    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get groups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202363060
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('group');
    }

    /**
     * Post groups by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202362870
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('group', $filename);
    }
}
