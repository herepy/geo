<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/8/13
 * Time: 16:21
 */

use Pengyu\Geo\GeoClient;

require_once "vendor/autoload.php";

$config=[
    "driver"    =>  "mongodb",
    "host"      =>  "localhost",
    "port"      =>  "27017",
];

$client=GeoClient::build($config);
$client->flush();
$client->add("chengdu",104.07,30.67);
$client->add("deyang",104.38,31.13);
$client->add("mianyang",104.67,31.46);

$client->bulk([
    ["name"=>"test1","lon"=>104.21,"lat"=>30.21],
    ["name"=>"test2","lon"=>105.12,"lat"=>31.69],
    ["name"=>"test3","lon"=>105.36,"lat"=>30.28],
]);

echo $client->distanceFrom("chengdu","mianyang",\Pengyu\Geo\Driver\DriverInterface::GEO_UNIT_KM);