<?php

namespace Wjohnson\AiFacescan\common\model;

use think\Model;

class UserFacescan extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $append = ['score_text'];

    /**
     * 关联用户表
     * @return \think\model\relation\HasOne
     */
    public function getUser()
    {
        return $this->hasOne('User', 'id', 'u_id');
    }

    /**
     * 获取得分文本属性
     */
    public function getScoreTextAttr($val, $row)
    {
        if (!isset($row['toily'])) {
            return '';
        }

        return
            __('Toily').":".$row['toily'].'; '.
            __('Uoily').":".$row['uoily'].'; '.
            __('Spot').':'.$row['spot'].'; '.
            __('Wrinkle').':'.$row['wrinkle'].'; '.
            __('Blackhead').':'.$row['blackhead'].'; '.
            __('Pore').':'.$row['pore'].'; '.
            __('Sensitive').':'.$row['sensitive'].'; '.
            __('Dark_circle').':'.$row['dark_circle'].'; ';
    }

    /**
     * @param $val
     * @param $row
     */
    public function getCreateTimeAttr($val, $row)
    {
        return date('Y-m-d H:i:s', $val);
    }

    /**
     * @param $val
     * @param $row
     */
    public function getUpdateTimeAttr($val, $row)
    {
        return date('Y-m-d H:i:s', $val);
    }
}
