<?php


namespace app\api\controller;

use Johnson\AiFacescan\api\service\AiscanService;

/**
 * 图文咨询
 */
class Facescan extends Base{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];
    // 本类对应的service层
    protected $service;
    public function __construct()
    {
        parent::__construct();
        $this->service = new AiscanService();
    }

    /**
     * 肌肤扫描记录
     */
    public function getList()
    {
        $where = [
            'u_id' => $this->auth->id,
        ];
        $this->request->get([
            'where' => $this->json($where),
            'field' => 'id,img,age,appearance,create_time',
        ]);
        parent::index();
    }

    /**
     * 肌肤扫描详细信息
     *
     * @param int /id/$id 查询id
     */
    public function getDetail($id = 0)
    {
        $where = [
            'u_id' => $this->auth->id,
            'id' => $id
        ];
        $this->request->get([
            'where' => $this->json($where)
        ]);
        parent::read($id);
    }

    /**
     * 肌肤扫描
     *
     * @param string $img 扫描的图片
     * @param string age 用户年龄
     */
    public function facescan($img = '', $age = '')
    {
        $result = $this->service->faceScan($this->auth->id, $img, $age);
        $this->success(__('Success'), $result);
    }

    /**
     * 获取一段时间的统计数据和最新一条数据的详细信息
     * @param string $start_date 开始日期
     * @param string $end_date 结束日期
     */
    public function getFacescanDataAndLast($start_date = '', $end_date = '')
    {
        $result = $this->service->getFacescanDataAndLast($this->auth->id, $start_date, $end_date);
        $this->success(__('Success'), $result);
    }
}
