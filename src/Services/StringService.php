<?php

namespace kosuha606\VirtualShop\Services;

/**
 * @package kosuha606\VirtualShop\Services
 */
class StringService
{
    public function map($data, $from, $to)
    {
        $result = [];

        foreach ($data as $datum) {
            $result[$datum[$from]] = $datum[$to];
        }

        return $result;
    }
}