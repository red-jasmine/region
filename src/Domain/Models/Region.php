<?php

namespace RedJasmine\Region\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Region\Enums\RegionLevel;
use RedJasmine\Support\Domain\Models\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Domain\Models\Traits\ModelTree;



class Region extends Model
{

    use HasDateTimeFormatter;

    use ModelTree;

    public $timestamps   = false;
    public $incrementing = false;

    protected $casts = [
        'level' => RegionLevel::class
    ];

    protected $fillable = [
        'parent_id', 'id', 'name', 'level', 'pinyin', 'pinyin_prefix'
    ];


    // 父级ID字段名称，默认值为 parent_id
    protected string $parentColumn = 'parent_id';
    // 排序字段名称，默认值为 order
    protected string $orderColumn = 'id';

    // 标题字段名称，默认值为 title
    protected string $titleColumn = 'name';

}
