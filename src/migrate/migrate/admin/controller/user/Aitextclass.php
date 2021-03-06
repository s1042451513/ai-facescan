<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use Wjohnson\AiFacescan\common\model\UserAiTextClass as InstanceModel;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Aitextclass extends Backend
{

    //模糊查询字段
    protected $searchFields = 'id,type,name,title';
    //无需登录的方法,同时也就不需要鉴权了
    protected $noNeedLogin = [''];

    /**
     * ArticleChannel模型对象
     * @var \app\admin\model\article\ArticleChannel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new InstanceModel();
    }

    public function selectpage()
    {
        $this->searchFields = 'type,name,title';
        $this->selectpageFields = 'id,title';
        return parent::selectpage(); // TODO: Change the autogenerated stub
    }

}
