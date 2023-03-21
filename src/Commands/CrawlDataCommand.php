<?php

namespace RedJasmine\Region\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use RedJasmine\Region\Enums\RegionLevel;

class CrawlDataCommand extends Command
{
    protected $signature = 'region:crawl-data';

    protected $description = 'Command description';

    public static string $baseUrl = 'http://www.stats.gov.cn/sj/tjbz/tjyqhdmhcxhfdm/2022/';

    public static array $tableClass = [
        'province' => '.provincetable .provincetr td', // 省
        'city'     => '.citytable .citytr', // 市
        'district' => '.countytable .countytr', // 县区
        'street'   => '.towntable .towntr', // 乡镇街道
        'village'  => '.villagetable .villagetr', //
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
            start:
            try {
                sleep(1);
                $response = Http::get($url);
                $body     = $response->body();
                Storage::disk('public')->put($file, $body);
            } catch (\Throwable $throwable) {
                $this->output->warning('请求报错');
                sleep(60);
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
        $dom  = new Dom();
        $dom->load($body);


        $results = $dom->find(self::$tableClass['province']);

        $lists = [];
        foreach ($results as $result) {
            /**
             * @var $result Dom\HtmlNode
             */
            $name = $result->find('a')->text;
            $href = $result->find('a')->getAttribute('href');

            preg_match("/\d+/", $href, $matches);
            $id = $matches[0];

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


    public function cities(array $parent)
    {
        $href = $parent['href'];
        $body = $this->getHtmlBody($href);
        $dom  = new Dom();
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

        $provinces = $this->provinces();
        foreach ($provinces as $province) {
            $cities = $this->cities($province);
            foreach ($cities as $city) {
                $districts = $this->districts($city);
                foreach ($districts as $district) {
                    $streets = $this->streets($district);
                    foreach ($streets as $street) {
                        $villages = $this->villages($street);
                    }
                }
            }

            dd($province);
        }

    }
}
