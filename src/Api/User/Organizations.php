<?php

namespace CybozuHttp\Api\User;

/**
 * @author ochi51 <ochiai07@gmail.com>
 */
class Organizations
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * Organizations constructor.
     * @param Csv $csv
     */
    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get organizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202124754
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getByCsv(): string
    {
        return $this->csv->get('organization');
    }

    /**
     * Post organizations by csv
     * https://cybozudev.zendesk.com/hc/ja/articles/202350640
     *
     * @param $filename
     * @return int
     * @throws \InvalidArgumentException
     */
    public function postByCsv($filename): int
    {
        return $this->csv->post('organization', $filename);
    }
}
