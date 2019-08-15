<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 13:04
 */

namespace Pengyu\Geo\Driver;

use Elasticsearch\ClientBuilder;

class ElasticSearchDriver extends BaseDriver
{
    protected $client;
    protected $index;
    protected $type;

    public function init(array $config)
    {
        $hosts=isset($config["host"]) ? $config["host"] : ["127.0.0.1"];
        $client=ClientBuilder::create()->setHosts($hosts)->build();

        $this->client=$client;
        $this->index=isset($config["index"]) ? $config["index"] : "geo_index";
        $this->type=isset($config["type"]) ? $config["db"] : "geo_type";
    }

    public function add(string $name, float $lon, float $lat): bool
    {
        if ($this->checkPoint($lon,$lat)) {
            return false;
        }

        $params=[
            "index" =>  $this->index,
            "type"  =>  $this->type,
            "body"  =>  [
                "name"      =>  $name,
                "location"  =>  [
                    "lat"   =>  $lat,
                    "lon"   =>  $lon
                ]
            ]
        ];
        $this->client->index($params);
        return true;
    }

    public function bulk(array $points): bool
    {
        $params=[];
        foreach ($points as $item) {
            if ($this->checkPoint($item["lon"],$item["lat"])) {
                return false;
            }

            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    '_type'  => $this->type,
                ]
            ];

            $params['body'][] = [
                'name'      => $item["name"],
                'location'  => [
                    "lat"   =>  $item["lat"],
                    "lon"   =>  $item["lon"]
                ]
            ];
        }
        $this->client->bulk($params);
        return true;
    }

    public function del(string $name): bool
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => $name
                    ]
                ]
            ]
        ];
        $this->client->deleteByQuery($params);
        return true;
    }

    public function flush(): bool
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'match_all' => []
                ]
            ]
        ];
        $this->client->deleteByQuery($params);
        return true;
    }

    public function distanceFrom(string $name1, string $name2, string $unit = self::GEO_UNIT_KM): float
    {
        $param1 = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => $name1
                    ]
                ]
            ]
        ];
        $point=$this->client->search($param1);
        if (empty($point["hits"]["hits"])) {
            return 0;
        }

        $point=$point["hits"]["hits"][0]["_source"];
        $param2=[
            "index" =>  $this->index,
            "type"  =>  $this->type,
            "body"  =>  [
                "query" =>  [
                    "match" =>  [
                        "name"  =>  $name2
                    ]
                ],
                "sort"  =>  [
                    "_geo_distance"  =>  [
                        "location"  =>  [
                            "lat"   =>  $point["location"]["lat"],
                            "lon"   =>  $point["location"]["lon"]
                        ],
                        "unit"  =>  $unit
                    ]
                ],
            "size"  => 1
            ]
        ];
        $result=$this->client->search($param2);

        if (empty($result["hits"]["hits"])) {
            return 0;
        }
        return $result["hits"]["hits"][0]["sort"];
    }

    public function radiusFrom(string $name,float $distance, string $unit = self::GEO_UNIT_KM, int $limit=10): array
    {
        $param1 = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => $name
                    ]
                ]
            ]
        ];
        $point=$this->client->search($param1);
        if (empty($point["hits"]["hits"])) {
            return [];
        }

        $point=$point["hits"]["hits"][0]["_source"];
        $param2=[
            "index" =>  $this->index,
            "type"  =>  $this->type,
            "body"  =>  [
                "query" =>  [
                    "bool"  =>  [
                        "filter"    =>  [
                            "geo_distance"  =>  [
                                "distance"  =>  $distance,
                                "location"  =>  [
                                    "lat"   =>  $point["location"]["lat"],
                                    "lon"   =>  $point["location"]["lon"]
                                ]
                            ]
                        ],
                    ]
                ],
                "sort"  =>  [
                    "_geo_distance"  =>  [
                        "location"  =>  [
                            "lat"   =>  $point["location"]["lat"],
                            "lon"   =>  $point["location"]["lon"]
                        ],
                        "unit"  =>  $unit
                    ]
                ],
                "size"  => $limit
            ]
        ];
        $result=$this->client->search($param2);

        if (empty($result["hits"]["hits"])) {
            return [];
        }

        $data=[];
        foreach ($result["hits"]["hits"] as $item) {
            $data[]=[
                $item["_source"]["name"],
                $item["sort"]
            ];
        }
        return $data;
    }
}