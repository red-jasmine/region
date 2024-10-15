<?php

namespace RedJasmine\Region\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Overtrue\LaravelPinyin\Facades\Pinyin;
use RedJasmine\Region\Domain\Models\Region;

class OptimizeCommand extends Command
{
    protected $signature = 'regions:optimize';

    protected $description = 'Command description';

    public function handle() : void
    {
        $pinyinService = app('pinyin');
        $cursor        = Region::where('pinyin', null)->cursor();
        foreach ($cursor as $region) {
            $pinyin                = $pinyinService->sentence($region->name);
            $region->pinyin_prefix = Str::upper($pinyin[0]);
            $region->pinyin        = $pinyin;
            $region->save();
        }
    }


}
