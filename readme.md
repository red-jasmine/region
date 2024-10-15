# Region

中华人民共和国行政区划（五级）：省级、地级、县级、乡级和村级

> [Gitee]  [https://gitee.com/red-jasmine/region](https://gitee.com/red-jasmine/region)

> [GitHub]  [https://github.com/red-jasmine/region](https://github.com/red-jasmine/region)

## 数据来源
- 国家统计局
  - [数据源 - https://www.stats.gov.cn/sj/tjbz/tjyqhdmhcxhfdm/2023/index.html](https://www.stats.gov.cn/sj/tjbz/tjyqhdmhcxhfdm/2023/index.html)
  - [数据源 - https://www.mca.gov.cn/mzsj/xzqh/2022/202201xzqh.html](https://www.mca.gov.cn/mzsj/xzqh/2022/202201xzqh.html)
  - [编码规范 - http://www.stats.gov.cn/sj/tjbz/gjtjbz/202302/t20230213_1902741.html](http://www.stats.gov.cn/sj/tjbz/gjtjbz/202302/t20230213_1902741.html)

> 2023年度全国统计用区划代码和城乡划分代码更新维护的标准时点调整为2023年6月30日。未包括我国台湾省、香港特别行政区和澳门特别行政区

## 城市等级

|       级别        | Level |
|:---------------:|:-----:|
|       国家        |   0   |
| 省、直辖市、自治区、特别行政区 |   1   |
|  地级市、地区、自治州、盟   |   2   |
|   县、县级市、区 、旗    |   3   |
|      乡镇街道       |   4   |
|    村、社区 、苏木     |   5   |


##  数据下载


| 数据                | SQL                                                                  |
|-------------------|----------------------------------------------------------------------|
| 省市区   三级          | [regions_level3.sql](https://github.com/red-jasmine/region/releases) |
| 省市区+乡镇街道    四级    | [regions_level4.sql](https://github.com/red-jasmine/region/releases) |   
| 省市区+乡镇街道 +社区、村 五级 | [regions_level5.sql](https://github.com/red-jasmine/region/releases) |


### 数据表

```sql
# 数据表
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned NOT NULL COMMENT '父级ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `pinyin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '拼音',
  `pinyin_prefix` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '首字母',
  `level` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB  COMMENT='行政区划表';
```




##  laravel 

## 安装

Via Composer

``` bash
$ composer require red-jasmine/region
```

## Usage
### Command

```php
# 爬取数据
php artisan regions:crawl-data
# 优化数据
php artisan regions:optimize

```

