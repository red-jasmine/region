<?php

namespace RedJasmine\Region\Enums;

enum RegionLevel: int
{
    case  PROVINCE = 0; // 省
    case  CITY = 1; // 市
    case  DISTRICT = 2; // 区、县
    case  STREET = 3; // 乡镇街道


    public static function options() : array
    {
        return [
            self::PROVINCE->value => '省',
            self::CITY->value     => '市',
            self::CITY->value     => '市',
            self::DISTRICT->value => '区|县',
            self::STREET->value   => '乡镇街道',
        ];
    }
}
