<?php

namespace RedJasmine\Region\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Overtrue\LaravelPinyin\Facades\Pinyin;
use RedJasmine\Region\Enums\RegionLevel;
use RedJasmine\Region\Models\Region;

class OptimizeCommand extends Command
{
    protected $signature = 'regions:optimize';

    protected $description = 'Command description';

    public function handle() : void
    {
        foreach (Region::cursor() as $region) {
            $pinyin                = app('pinyin');
            $region->pinyin_prefix = Str::upper($pinyin->abbr($region->name)[0]);
            $region->pinyin        = $pinyin->sentence($region->name);
            $region->save();
        }
    }


}
