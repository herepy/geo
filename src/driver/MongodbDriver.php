<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 13:04
 */

namespace Pengyu\Geo\Driver;

use MongoDB\Client;

class MongodbDriver extends BaseDriver
{
    protected $db;
    protected $collection;
    protected $collectionName;

    public function init(array $config)
    {
        $host=isset($config["host"]) ? $config["host"] : "127.0.0.1";
        $port=isset($config["port"]) ? $config["port"] : "27017";
        $uri="mongodb://".$host.":".$port;
        $db=isset($config["db"]) ? $config["db"] : "geo_db";
        $collection=isset($config["collection"]) ? $config["collection"] : "geo_collection";

        $client=new Client($uri);
        $this->db=$client->$db;
        $this->collection=$this->db->$collection;
        $this->collectionName=$collection;
    }

    public function add(string $name, float $lon, float $lat): bool
    {
        if (!$this->checkPoint($lon,$lat)) {
            return false;
        }

        $document=[
            "name"      =>  $name,
            "location"  =>  [
                "type"          =>  "Point",
                "coordinates"   =>  [$lon,$lat]
            ]
        ];
        $this->collection->insertOne($document);
        return true;
    }

    public function bulk(array $points): bool
    {
        $documents=[];
        foreach ($points as $item) {
            if (!$this->checkPoint($item["lon"],$item["lat"])) {
                return false;
            }
            $documents[]=[
                "name"      =>  $item["name"],
                "location"  =>  [
                    "type"          =>  "Point",
                    "coordinates"   =>  [$item["lon"],$item["lat"]]
                ]
            ];
        }

        $this->collection->insertMany($documents);
        return true;
    }

    public function del(string $name): bool
    {
        $this->collection->deleteOne(["name"=>$name]);
        return true;
    }

    public function flush(): bool
    {
        $this->collection->deleteMany([]);
        return true;
    }

    public function distanceFrom(string $name1, string $name2, string $unit = "m"): float
    {
        $point=$this->collection->findOne(["name"=>$name1]);
        if (!$point) {
            return 0;
        }
        $cursor=$this->db->command([
            'geoNear' => $this->collectionName,
            'near' => [
                'type' => 'Point',
                'coordinates' => (array)$point["location"]["coordinates"],
            ],
            'spherical' => 'true',
            'num' => 1,
            'query' =>  ["name"=>$name2]
        ]);

        $results = $cursor->toArray()[0];
        if (count($results["results"]) == 0) {
            return 0;
        }

        $distence=$results["results"][0]["dis"];
        if ($unit == "km") {
            $distence=$distence/1000;
        }

        return $distence;
    }

    public function radiusFrom(string $name,float $distance, string $unit = "m", int $limit=10): array
    {
        $point=$this->collection->findOne(["name"=>$name]);
        if (!$point) {
            return [];
        }
        $distance=abs($unit == "m" ? $distance : $distance*1000);

        $cursor=$this->db->command([
            'geoNear' => $this->collectionName,
            'near' => [
                'type' => 'Point',
                'coordinates' => (array)$point["location"]["coordinates"],
            ],
            'spherical' => 'true',
            'num' => $limit,
            'maxDistance'   =>  $distance
        ]);

        $results = $cursor->toArray()[0];
        $results=$results["results"];

        $data=[];
        foreach ($results as $item) {
            $info=[
                $item["obj"]["name"],
                $unit == "km" ? $item["dis"]/1000 : $item["dis"]
            ];
            $data[]=$info;
        }

        return $data;
    }
}