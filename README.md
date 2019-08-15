### 简介
一个简单位置服务的库，多种驱动方式实现。

### 功能
* 两地距离计算
* 查找附近的点

### 安装
##### 必要条件
目前可使用驱动有：redis(默认)、mongodb、elasticsearch,驱动可通过参数配置切换，使用某个驱动时，必须安装对应的软件，比如：redis >= 3.2.0,mongodb >= 2.4等

##### git方式
```shell
    git clone https://github.com/herepy/geo.git
    cd geo && composer install
```
##### composer方式
```shell
    composer require pengyu/geo
```
##### 引入项目
```php
    use Pengyu\Geo\GeoClient;
    require "vendor/autoload.php";
```

### 示例
##### 初始化实例
```php
//使用mongodb驱动
$config=[
    "driver"    =>  "mongodb",
    "host"      =>  "localhost",
    "port"      =>  "27017",
];
//使用redis驱动
$config=[
    "driver"    =>  "redis",
    "host"      =>  "localhost",
    "port"      =>  "6379",
    "password"  =>  "12345"
];
$client=GeoClient::build($config);
```
##### 添加坐标点
```php
    //单个添加
    $client->add("chengdu",104.07,30.67);
    $client->add("deyang",104.38,31.13);
    $client->add("mianyang",104.67,31.46);
    //批量添加
    $client->bulk([
        ["name"=>"test1","lon"=>104.21,"lat"=>30.21],
        ["name"=>"test2","lon"=>105.12,"lat"=>31.69],
        ["name"=>"test3","lon"=>105.36,"lat"=>30.28],
    ]);
```
##### 删除
```php
    //删除单个坐标
    $client->del("chengdu");
    //清空所有坐标
    $client->flush();
```
##### 距离计算
```php
    //距离单位可选，默认为千米
    $client->distanceFrom("chengdu","mianyang",\Pengyu\Geo\Driver\DriverInterface::GEO_UNIT_KM)
```
##### 查找附近的点
```php
    //距离单位可选，默认为千米  返回格式：[["chengdu",0],["deyang",59.15161],["mianyang",104.9137]]
    $client->radiusFrom("chengdu",150000,\Pengyu\Geo\Driver\DriverInterface::GEO_UNIT_M);
```