<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use Wjohnson\AiFacescan\common\model\UserFacescan as UserFacescanModel;
use Wjohnson\AiFacescan\common\model\User;

/**
 * 用户肌肤分析管理
 *
 * @icon fa fa-circle-o
 */
class Aifacescan extends Backend
{

    protected $noNeedRight = ['selectpageuser'];
    //模糊查询字段
    protected $searchFields = 'id';
    /**
     * UserFeedback模型对象
     * @var \app\admin\model\UserFeedback
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new UserFacescanModel();
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 肌肤分析列表
     */
    public function index()
    {
        $this->model->append(['score_text']);
        $this->model->with([
                'getUser' => function($user){
                    $user->field('id, nickname, mobile, prevtime, logintime, jointime');
                },
            ]);
        return parent::index();
    }

    /**
     * 不允许添加
     * @return bool
     */
    public function add()
    {
        $this->error('非法操作');
    }

    /**
     * 处理用户肌肤分析
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->alias('f')->join('User u', 'f.u_id = u.id', 'left')->field('f.*, username')->where('f.id', $ids)->find();
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $params['admin_id'] = $this->auth->id;
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 不能删除会员肌肤分析
     * @param string $ids
     */
    public function del($ids = "")
    {
        $this->error('不能删除会员肌肤分析');
    }


    /**
     * 下拉框数据
     */
    public function selectpageuser()
    {
        $this->model = new User();
        $this->selectpageFields = 'id, concat(nickname,"(",mobile,")") nickname';
        $this->request->request([
            "searchField" => ["id", "nickname"],
            "andOr" => "or",
        ]);
        return parent::selectpage();
    }
}
