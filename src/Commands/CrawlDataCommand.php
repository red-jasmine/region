<?php

namespace RedJasmine\Region\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use RedJasmine\Region\Enums\RegionLevel;
use RedJasmine\Region\Models\Region;

class CrawlDataCommand extends Command
{
    protected $signature = 'regions:crawl-data {level=3}';

    protected $description = '数据爬取';

    public static string $baseUrl = 'http://www.stats.gov.cn/sj/tjbz/tjyqhdmhcxhfdm/2022/';

    public static array $tableClass = [
        'province'        => '.provincetable .provincetr td', // 省
        'city'            => '.citytable .citytr', // 市
        'district'        => '.countytable .countytr', // 县区
        'district_towntr' => '.countytable .towntr', // 县区 街道 如东莞
        'street'          => '.towntable .towntr', // 乡镇街道
        'village'         => '.villagetable .villagetr', //
    ];


    public function districts(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);
        $dom        = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['district']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $href = null;
            if ($result->find('td')[0]->find('a')->count() > 0) {
                $id   = $result->find('td a')[0]->text;
                $name = $result->find('td a')[1]->text;
                $href = $result->find('td a')[0]->getAttribute('href');
                $href = Str::replace(Str::match("/\d+\.html/", $parentHref), $href, $parentHref);
            } else {
                $id   = $result->find('td')[0]->text;
                $name = $result->find('td')[1]->text;
            }
            $lists[] = [
                'id'        => str_pad($id, 12, 0),
                'name'      => $name,
                'href'      => $href,
                'parent_id' => $parent['id'],
                'level'     => RegionLevel::DISTRICT->value
            ];

        }

        return $lists;

    }

    public function findData(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);

        $dom = new Dom();
        $dom->load($body);

        // 解析 县区
        $districts = $this->districts($parent);
        // 解析 县区街道 如东莞市 下面直接是街道
        $districtStreets = $this->districtStreets($parent);
        return array_merge($districts, $districtStreets);
    }


    public function districtStreets(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);
        $dom        = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['district_towntr']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $id = null;

            $name = null;
            $href = null;

            if ($result->find('td')[0]->find('a')->count() > 0) {
                $id   = $result->find('td a')[0]->text;
                $name = $result->find('td a')[1]->text;
                $href = $result->find('td a')[0]->getAttribute('href');
                $href = Str::replace(Str::match("/[\d]+\.html/", $parentHref), $href, $parentHref);

            } else {
                $id   = $result->find('td')[0]->text;
                $name = $result->find('td')[1]->text;
            }

            $lists[] = [
                'id'        => str_pad($id, 12, 0),
                'name'      => $name,
                'href'      => $href,
                'parent_id' => $parent['id'],
                'level'     => RegionLevel::STREET->value
            ];

        }

        return $lists;

    }

    public function streets(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);
        $dom        = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['street']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $id = null;

            $name = null;
            $href = null;

            if ($result->find('td')[0]->find('a')->count() > 0) {
                $id   = $result->find('td a')[0]->text;
                $name = $result->find('td a')[1]->text;
                $href = $result->find('td a')[0]->getAttribute('href');
                $href = Str::replace(Str::match("/[\d]+\.html/", $parentHref), $href, $parentHref);

            } else {
                $id   = $result->find('td')[0]->text;
                $name = $result->find('td')[1]->text;
            }

            $lists[] = [
                'id'        => str_pad($id, 12, 0),
                'name'      => $name,
                'href'      => $href,
                'parent_id' => $parent['id'],
                'level'     => RegionLevel::STREET->value
            ];

        }

        return $lists;

    }

    public function villages(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);
        $dom        = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['village']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $id   = null;
            $name = null;
            $href = null;

            if ($result->find('td')[0]->find('a')->count() > 0) {

                $id          = $result->find('td a')[0]->text;
                $villageCode = $result->find('td a')[1]->text;
                $name        = $result->find('td a')[2]->text;

            } else {
                $id          = $result->find('td')[0]->text;
                $villageCode = $result->find('td')[1]->text;
                $name        = $result->find('td')[2]->text;
            }

            $lists[] = [
                'id'           => str_pad($id, 12, 0),
                'name'         => $name,
                'href'         => $href,
                'parent_id'    => $parent['id'],
                'village_code' => $villageCode,
                'level'        => RegionLevel::VILLAGE->value
            ];

        }

        return $lists;
    }


    /**
     * 获取页面数据
     * @param $orgHref
     * @return string
     */
    public function getHtmlBody($orgHref) : string
    {
        if (blank($orgHref)) {
            return '';
        }
        $url  = self::$baseUrl . $orgHref;
        $dir  = 'regions/';
        $file = $dir . $orgHref;
        if (!Storage::disk('public')->exists($file)) {
            $try = 0;
            start:
            try {
                $response = Http::get($url);
                $body     = $response->body();
                Storage::disk('public')->put($file, $body);
                $this->output->success($orgHref);
            } catch (\Throwable $throwable) {
                $try++;
                $this->output->warning('请求报错 等待1分钟');
                $time = 10 * $try;
                sleep($time);
                goto  start;
            }

        } else {
            $body = Storage::disk('public')->get($file);
        }
        return $body;
    }


    public function provinces() : array
    {
        $body = $this->getHtmlBody('index.html');

        $dom = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['province']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $name = $result->find('a')->text;
            $href = $result->find('a')->getAttribute('href');
            preg_match("/\d+/", $href, $matches);
            $id      = $matches[0];
            $lists[] = [
                'id'        => str_pad($id, 12, 0),
                'name'      => $name,
                'href'      => $href,
                'parent_id' => 0,
                'level'     => RegionLevel::PROVINCE->value
            ];

        }

        return $lists;

    }


    public function cities(array $parent) : array
    {
        $parentHref = $parent['href'];
        $body       = $this->getHtmlBody($parentHref);
        $dom        = new Dom();
        $dom->load($body);
        $results = $dom->find(self::$tableClass['city']);
        $lists   = [];
        foreach ($results as $result) {
            /**
             * @var $city Dom\HtmlNode
             */

            $id      = $result->find('td a')[0]->text;
            $href    = $result->find('td a')[0]->getAttribute('href');
            $name    = $result->find('td a')[1]->text;
            $lists[] = [
                'id'        => str_pad($id, 12, 0),
                'name'      => $name,
                'href'      => $href,
                'parent_id' => $parent['id'],
                'level'     => RegionLevel::CITY->value
            ];

        }
        return $lists;
    }

    public function handle() : void
    {

        $level = (int)$this->argument('level');


        $provinces = $this->provinces();
        //$this->toDB($provinces);
        foreach ($provinces as $province) {
            $cities = $this->cities($province);
            $this->toDB($cities);
            foreach ($cities as $city) {
                if ($city['name'] === '省直辖县级行政区划' || $city['name'] === '自治区直辖县级行政区划') {
                    $city['id'] = $province['id']; // 替换ID
                }
                $districts = $this->findData($city);
                //$this->toDB($districts);
                if ($level <= RegionLevel::DISTRICT->value) {
                    continue;
                }
                foreach ($districts as $district) {
                    // 判断是乡镇还是县区
                    if ($district['level'] === RegionLevel::DISTRICT->value) {
                        $streets = $this->streets($district);
                    } else {
                        $streets = $this->villages($district);
                    }
                    //$this->toDB($streets);
                    if ($level <= RegionLevel::STREET->value) {
                        continue;
                    }
                    foreach ($streets as $street) {
                        if ($street['level'] === RegionLevel::STREET->value) {
                            $villages = $this->villages($street);
                            $this->toDB($villages);
                        }

                    }
                }
            }
        }

    }


    public function toDB(array $lists) : void
    {
        $pinyin = app('pinyin');
        foreach ($lists as $list) {

            if ($list['level'] === RegionLevel::DISTRICT->value && $list['name'] === '市辖区') {
                continue;
            }
            if ($list['level'] === RegionLevel::CITY->value && $list['name'] === '省直辖县级行政区划') {
                continue;
            }
            if ($list['level'] === RegionLevel::CITY->value && $list['name'] === '自治区直辖县级行政区划') {
                continue;
            }
            Region::updateOrCreate([
                                       'id' => (int)$list['id'],
                                   ],
                                   [
                                       'id'            => (int)$list['id'],
                                       'parent_id'     => (int)$list['parent_id'],
                                       'name'          => $list['name'],
                                       'level'         => $list['level'],
                                       //'pinyin_prefix' => Str::upper($pinyin->abbr($list['name'])[0]),
                                       //'pinyin'        => $pinyin->sentence($list['name'])
                                   ]
            );

        }
    }
}
