<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('ID');
            $table->unsignedBigInteger('parent_id')->comment('父级ID');
            $table->string('name')->nullable()->comment('名称');
            $table->string('pinyin')->nullable()->comment('拼音');
            $table->string('pinyin_prefix', 1)->nullable()->comment('首字母');
            $table->string('level')->comment(\RedJasmine\Region\Domain\Enums\RegionLevelEnum::comments('级别'));
            $table->comment('行政区划表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('regions');
    }
};
