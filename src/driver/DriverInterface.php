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
    /**
     * array 有效经度范围
     */
    const GEO_LON_RANG=[-180,180];

    /**
     * array 有效维度范围，越靠近极点越不准确，所有没有到90
     */
    const GEO_LAT_RANG=[-85,85];

    /**
     * 初始化
     * @param array $config 配置参数
     * @return mixed
     */
    public function init(array $config);

    /**
     * 添加坐标点
     * @param string $name 坐标名
     * @param float $lon 经度
     * @param float $lat 维度
     * @return bool
     */
    public function add(string $name,float $lon,float $lat):bool ;

    /**
     * 批量添加坐标点
     * @param array $points
     * @return bool
     */
    public function bulk(array $points):bool ;

    /**
     * 删除坐标点
     * @param string $name 坐标名
     * @return bool
     */
    public function del(string $name):bool ;

    /**
     * 清空坐标点
     * @return bool
     */
    public function flush():bool ;

    /**
     * 计算两个坐标点距离
     * @param string $name1 坐标点名1
     * @param string $name2 坐标点名2
     * @param string $unit 距离单位 m|km
     * @return float
     */
    public function distanceFrom(string $name1,string $name2,string $unit="m"):float ;

    /**
     * 坐标点周围的坐标点列表
     * @param string $name 中心坐标点名
     * @param int $limit 返回数量
     * @param string $unit 距离单位 m|km
     * @return float
     */
    public function radiusFrom(string $name,int $limit=10,string $unit="m"):float ;

}