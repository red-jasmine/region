<?php

namespace RedJasmine\Region\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum RegionLevelEnum: string
{

    use EnumsHelper;


    case  COUNTRY = 'country'; // 国家
    case  PROVINCE = 'province'; // 省
    case  CITY = 'city'; // 市
    case  DISTRICT = 'district'; // 区、县
    case  STREET = 'street'; // 乡镇街道
    case  VILLAGE = 'village'; // 村庄

    public function labels() : array
    {
        return [
            self::COUNTRY->value  => '国家',
            self::PROVINCE->value => '省',
            self::CITY->value     => '市',
            self::DISTRICT->value => '区、县、市',
            self::STREET->value   => '街道乡镇',
            self::VILLAGE->value  => '村、社区',
        ];
    }
}
