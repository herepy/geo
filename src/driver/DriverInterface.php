<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 12:15
 */

namespace Pengyu\Geo\Driver;

interface DriverInterface
{
    const GEO_LON_RANG=[-180,180];
    const GEO_LAT_RANG=[-85,85];  //有效范围，越靠近极点越不准确

    public function init(array $config);

    public function add(string $name,float $lon,float $lat):bool ;

    public function bulk(array $points):bool ;

    public function del(string $name):bool ;

    public function flush():bool ;

    public function distanceFrom(string $name1,string $name2,string $unit="m"):float ;

    public function radiusFrom(string $name,int $limit,string $unit="m"):float ;

}