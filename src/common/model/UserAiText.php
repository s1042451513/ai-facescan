<?php

namespace Wjohnson\AiFacescan\common\model;

use think\Model;

class UserAiText extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 获取分类
     * @return \think\model\relation\HasOne
     */
    public function getClass()
    {
        return $this->hasOne("UserAiTextClass", 'id', 'c_id');
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
