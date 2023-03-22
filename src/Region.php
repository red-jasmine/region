<?php

namespace RedJasmine\Region;

use Illuminate\Database\Eloquent\Collection;
use RedJasmine\Region\Enums\RegionLevel;
use RedJasmine\Region\Models\Region as RegionModel;

class Region
{
    // Build wonderful things


    public function find(int $id) : RegionModel
    {
        return RegionModel::find($id);
    }


    /**
     * 查询区划数据
     * @param array|int|string $id
     * @return  RegionModel[]|Collection|array
     */
    public function query(array|int|string $id) : array|Collection
    {
        if (is_array($id)) {
            $id = array_filter($id);
        } else {
            $id = [ (int)$id ];
        }
        if (blank($id)) {
            return [];
        }
        return RegionModel::whereIn('id', $id)->get();
    }

    /**
     * 省份
     * @return RegionModel[]|Collection
     */
    public function provinces() : array|Collection
    {
        return RegionModel::where('parent_id', 0)
                          ->where('level', RegionLevel::PROVINCE->value)
                          ->get();
    }

    /**
     * 查询子集
     * @param int $parentID
     * @return RegionModel[]|Collection
     */
    public function children(int $parentID) : array|Collection
    {
        return RegionModel::where('parent_id', (int)$parentID)->get();
    }

    /**
     *
     * @param int|string $province
     * @return Models\Region[]|array|Collection
     */
    public function cities(int|string $province) : Collection|array
    {
        $query = RegionModel::query();
        if (is_numeric($province)) {
            return $this->children($province);
        } else {
            $name = $province;
            return $query->where('name', (string)$name)->get();
        }

    }
}
