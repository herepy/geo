<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 15:08
 */

namespace Pengyu\Geo\Driver;

class BaseDriver implements DriverInterface
{
    public function init(array $config){}

    public function add(string $name, float $lon, float $lat): bool{}

    public function bulk(array $points): bool{}

    public function del(string $name): bool{}

    public function flush(): bool{}

    public function distanceFrom(string $name1, string $name2, string $unit = "m"): float{}

    public function radiusFrom(string $name,float $distance,string $unit="m",int $limit=10): float{}

    protected function checkPoint(float $lon,float $lat):bool
    {
        if ($lat > self::GEO_LAT_RANG[1] || $lat < self::GEO_LAT_RANG[0] || $lon > self::GEO_LON_RANG[1] || $lon < self::GEO_LON_RANG[0]) {
            return false;
        }

        return true;
    }

}