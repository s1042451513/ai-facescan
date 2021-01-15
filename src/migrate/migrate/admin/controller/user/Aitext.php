<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use Wjohnson\AiFacescan\common\model\UserAiText as InstanceModel;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Aitext extends Backend
{

    //模糊查询字段
    protected $searchFields = 'id,title';
    /**
     * Article模型对象
     * @var \app\admin\model\article\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new InstanceModel();
    }
    
    /**
     * 获取文章列表
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $this->model->with('getClass');
            $this->request->get([
                'sort' => 'c_id desc, start_score desc',
                'order' => ' '
            ]);
        }
        return parent::index();
    }

    /**
     * 添加数据
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param('row/a');
            $this->checkSetScore($param['c_id'],$param['start_score'],$param['end_score']);
        }
        return parent::add();
    }

    /**
     * 编辑数据
     * @return mixed
     */
    public function edit($ids = null)
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param('row/a');
            $this->checkSetScore($ids, $param['c_id'],$param['start_score'],$param['end_score']);
        }
        return parent::edit($ids);
    }

    /**
     * 检测要设置的分数是否存在
     * @param int $currentId 当前id
     * @param string $cId 分类id
     * @param int $start_score 开始分数
     * @param int $end_score 结束分数
     * @return bool
     */
    private function checkSetScore($currentId, $cId = '', $start_score = 0, $end_score = 0)
    {
        // 参数验证
        if (empty($cId) || (empty($start_score) && $start_score != 0) || (empty($end_score) && $end_score != 0)) {
            $this->error('参数异常');
        }
        if ($start_score > $end_score) {
            $this->error('结束分必须大于开始分');
        }

        $where = "id <> {$currentId} && (c_id = {$cId} and start_score <= {$start_score} and end_score >= {$start_score}) or (c_id = {$cId} and start_score <= {$end_score} and end_score >= {$end_score})";
        $count = $this->model->where($where)->count();
        if ($count) {
            $this->error("所设置的分数值存在冲突");
        }

        return true;
    }
}
