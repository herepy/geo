<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 13:04
 */

class ElasticSearchDriver implements DriverInterface
{
    public function add(string $name, float $lon, float $lat): bool
    {
        // TODO: Implement add() method.
    }

    public function bulk(array $points): bool
    {
        // TODO: Implement bulk() method.
    }

    public function del(string $name): bool
    {
        // TODO: Implement del() method.
    }

    public function flush(): bool
    {
        // TODO: Implement flush() method.
    }

    public function distanceFrom(string $name1, string $name2, string $unit = "m"): float
    {
        // TODO: Implement distanceFrom() method.
    }

    public function radiusFrom(string $name, int $limit, string $unit = "m"): float
    {
        // TODO: Implement radiusFrom() method.
    }
}