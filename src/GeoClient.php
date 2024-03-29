<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 12:14
 */

namespace Pengyu\Geo;


use Pengyu\Geo\Driver\DriverInterface;

class GeoClient
{
    protected $driver;

    protected static $instance;

    protected function __construct(){}

    protected static $availableDriver=[
        "redis"         =>  "Pengyu\\Geo\\Driver\\RedisDriver",
        "mongodb"       =>  "Pengyu\\Geo\\Driver\\MongodbDriver",
        "elasticsearch" =>  "Pengyu\\Geo\\Driver\\ElasticSearchDriver"
    ];

    public static function build(array $config)
    {

        if (self::$instance) {
            return self::$instance;
        }

        self::$instance=new self();
        if (!isset($config["driver"]) || !array_key_exists($config["driver"],self::$availableDriver)) {
            $driverName=self::$availableDriver["redis"];
        } else {
            $driverName=self::$availableDriver[$config["driver"]];
        }

        $driver=new $driverName;
        $driver->init($config);
        self::$instance->driver=$driver;

        return self::$instance;
    }

    public function add(string $name, float $lon, float $lat)
    {
        return $this->driver->add($name,$lon,$lat);
    }

    public function bulk(array $points)
    {
        return $this->driver->bulk($points);
    }

    public function del(string $name)
    {
        return $this->driver->del($name);
    }

    public function flush()
    {
        return $this->driver->flush();
    }

    public function distanceFrom(string $name1, string $name2, string $unit = DriverInterface::GEO_UNIT_KM)
    {
        return $this->driver->distanceFrom($name1,$name2,$unit);
    }

    public function radiusFrom(string $name,float $distance,string $unit=DriverInterface::GEO_UNIT_KM,int $limit=10)
    {
        return $this->driver->radiusFrom($name,$distance,$unit,$limit);
    }
}