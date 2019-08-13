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
    "host"      =>  "localhost",
    "port"      =>  6379,
    "db"        =>  0,
    "key"       =>  "test_geo",
    "password"  =>  "1993918py"
];

$client=GeoClient::build($config);
$client->add("chengdu",104.07,30.67);
$client->add("deyang",104.38,31.13);
$client->add("mianyang",104.67,31.46);


echo $client->distanceFrom("chengdu","mianyang","km")."\n";
var_dump($client->radiusFrom("chengdu",100,"km"));