<?php

namespace kosuha606\VirtualShop\Services;

class StringService
{
    /**
     * @param $data
     * @param $from
     * @param $to
     * @return array
     */
    public function map($data, $from, $to)
    {
        $result = [];

        foreach ($data as $datum) {
            $result[$datum[$from]] = $datum[$to];
        }

        return $result;
    }
}
