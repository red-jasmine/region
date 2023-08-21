<?php

namespace RedJasmine\Region\Models;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Support\Traits\HasDateTimeFormatter;

class Region extends Model
{

    use HasDateTimeFormatter;

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable =[
        'parent_id','id','name','level','pinyin','pinyin_prefix'
    ];

}
