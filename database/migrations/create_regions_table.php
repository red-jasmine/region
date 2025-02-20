<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RedJasmine\Region\Domain\Enums\RegionLevelEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create(config('red-jasmine-region.tables.prefix', 'jasmine_').'regions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('编码');
            $table->string('name')->comment('名称');
            $table->string('parent_code')->nullable()->comment('编码');
            $table->string('level', 32)->comment(RegionLevelEnum::comments('级别'));
            $table->string('initial', 1)->nullable()->comment('首字母');
            $table->string('pinyin')->nullable()->comment('拼音');
            $table->comment('行政区划表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists(config('red-jasmine-region.tables.prefix', 'jasmine_').'regions');
    }
};
