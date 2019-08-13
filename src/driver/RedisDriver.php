<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 13:03
 */

namespace Pengyu\Geo\Driver;

class RedisDriver extends BaseDriver
{
    protected $instance;
    protected $key;

    public function init(array $config)
    {
        $host=isset($config["host"]) ? $config["host"] : "localhost";
        $port=isset($config["port"]) ? $config["port"] : "6379";
        $db=isset($config["db"]) ? $config["db"] : 0;

        $redis=new \Redis();
        $redis->connect($host,$port);

        if (isset($config["password"])) {
            $redis->auth($config["password"]);
        }
        $redis->select($db);

        $this->key=isset($config["key"]) ? $config["key"] : "geo_collection";
        $this->instance=$redis;
    }

    public function add(string $name, float $lon, float $lat): bool
    {
        if (!$this->checkPoint($lon,$lat)) {
            return false;
        }

        $this->instance->geoAdd($this->key,$lon,$lat,$name);
        return true;
    }

    public function bulk(array $points): bool
    {
        $flag=true;
        $this->instance->multi();

        foreach ($points as $item) {
            if (!$this->checkPoint($item["lon"],$item["lat"])) {
                $flag=false;
                break;
            }

            $this->instance->geoAdd($this->key,$item["lon"],$item["lat"],$item["name"]);
        }

        if (!$flag) {
            $this->instance->discard();
            return false;
        }

        $this->instance->exec();
        return true;
    }

    public function del(string $name): bool
    {
        return $this->instance->zRem($this->key,$name) ? true : false;
    }

    public function flush(): bool
    {
        return $this->instance->del($this->key) ? true : false;
    }

    public function distanceFrom(string $name1, string $name2, string $unit = "m"): float
    {
        return $this->instance->geoDist($this->key,$name1,$name2,$unit);
    }

    public function radiusFrom(string $name,float $distance,string $unit="m",int $limit=10): float
    {

        return $this->instance->rawCommand("GEORADIUSBYMEMBER",$this->key,$name,$distance,$unit,"WITHDIST","COUNT",$limit);
    }
}