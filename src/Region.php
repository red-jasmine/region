<?php

namespace RedJasmine\Region;

class Region
{
    // Build wonderful things

    public function find(int $id) : \RedJasmine\Region\Models\Region
    {
        return \RedJasmine\Region\Models\Region::find($id);
    }
}
