<?php


namespace Wjohnson\AiFacescan\common\service;

use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use think\Db;
/**
 * 服务层基类
 */
class BaseService
{
    /**
     * @var Request Request 实例
     */
    protected $request;

    /**
     * 默认响应输出类型,支持json/xml
     * @var string
     */
    protected $responseType = 'json';

    /**
     * @var Object Model 实例
     */
    protected $model;

    /**
     * @var bool|string Validate 类型
     */
    protected $validate = true;

    /**
     * @var bool|string Validate 类型
     */
    protected $scene = true;

    /**
     * @var bool|string allowField 类型
     */
    protected $allowField = false;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * @var String error 错误
     */
    protected $error;

    /**
     * @var int code 返回码
     */
    protected $code;

    /**
     * @var array|string data 返回的数据
     */
    protected $data;

    /**
     * @var bool Transaction 启动事务
     */
    protected static $startTrans = false;

    /**
     * @var bool lock 是否加锁
     */
    protected $lock = false;

    /**
     * 构造方法
     * BaseService constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;

        $this->_initialize();

        if (!empty($this->model)) {
            $modelPath = str_replace("\\", '/', get_class($this->model));
            $this->validate = $this->validate ??  (dirname($modelPath) .  basename($modelPath));
        }
    }

    /**
     * 初始化函数
     */
    protected function _initialize(){}

    /**
     * 查询
     * @param string $where 查询条件
     * @param string $field 查询字段
     * @param string $limit 查询数量
     * @param null $order   排序
     * @return array|void   查询的结果集
     */
    public function select($where = '1=1', $field = '*', $limit = '0,10', $order = NULL, $searchList = [])
    {
        $result = $this->model->where($where)->where($searchList)->field($field)->limit($limit)->order($order  ?: ($order !== false ? $this->model->getPk().' desc' : ''))->lock($this->lock)->select();
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 分组统计方法
     * @param string $where
     * @param string $field
     * @param null $order
     * @param string $groupBy
     */
    public function group($where = '1=1', $field = '*', $groupBy = '', $order = NULL)
    {
        $result = $this->model->where($where)->field($field)->order($order ?: $this->model->getPk().' desc')->lock($this->lock)->group($groupBy)->select();
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 查询单条
     * @param string $where 查询条件
     * @param string $field 查询字段
     * @param null $order   排序
     * @return array|void   查询的结果集
     */
    public function  find($where = '1=1', $field = '*')
    {
        $result = $this->model->where($where)->field($field)->lock($this->lock)->find();
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 查询单条
     * @param string $value 查询的值
     * @param string $where 查询条件
     * @param null $order   排序
     * @return array|void   查询的结果
     */
    public function value($value, $where = '1=1')
    {
        $result = $this->model->where($where)->lock($this->lock)->value($value);
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 增加
     * @param array $data 要新增的数据
     */
    public function insert($data)
    {
        $scene = ($this->scene === false ? '' : is_bool($this->scene) ? '.add' : '.'.$this->scene);
        $this->validate && is_bool($this->validate) && $this->validate = basename(str_replace('\\', '/', get_class($this->model))).$scene;
        $this->model->validate($this->validate);
        $this->allowField && $this->model->allowField($this->allowField);
        $result = !empty($data[0]) && is_array($data[0]) ? $this->model->saveAll($data, false) : $this->model->isUpdate(false)->save($data) ;
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 修改
     * @param array $data   要修改的数据
     * @param string $where 修改条件
     */
    public function update( $data = [], $where = NULL)
    {
        $scene = ($this->scene === false ? '' : is_bool($this->scene) ? '.edit' : '.' . $this->scene);
        $this->validate && is_bool($this->validate) && $this->validate = basename(str_replace('\\', '/', get_class($this->model))).$scene;
        $this->model->validate($this->validate);
        $this->allowField && $this->model->allowField($this->allowField);
        $result = !empty($data[0]) && is_array($data[0]) ? $this->model->saveAll($data) : $this->model->isUpdate(true)->save($data, $where);
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 删除
     * @param int $id   要删除的条件id
     */
    public function delete($id)
    {
        $result = $this->model->destroy($id);
        return $result === false
            ? $this->returnError($this->model->getError())
            : $result;
    }

    /**
     * 生成查询参数
     * @return array 返回生成的查询参数
     */
    public function buildParem($join = false)
    {
        $tableName = $join !== false ? $this->model->getTable().'.' : '';

        $field = ($this->request->get("field", NULL) ?: '*');
        $where = ($this->request->get("where", NULL, NULL) ?: NULL);
        $order = ($this->request->param('order', NULL) ?: $tableName.$this->model->getPk());
        $sort = ($this->request->param('sort', NULL) ?: 'desc');
        $limit = ($this->request->param('limit', NULL) ?: '0,10');
        $searchlist = ($this->request->param('searchlist', NULL, NULL) ?: false);

        !empty($where) && $where = is_array($where) ? $where : json_decode($where, true);

        if ($tableName) {
            $field = explode(',', $field);
            $joinSign = ','.$tableName;
            $field = $tableName.implode($joinSign, $field);

            foreach ($where as $key => $condition) {
                $where[$tableName.$key] = $condition;
                unset($where[$key]);
            }
        }

        $limits = explode(',', $limit);
        !empty($limits[1]) && $limit =(($limits[0] > 0 ? ($limits[0] - 1) : 0)* $limits[1]) . ',' . $limits[1];

        // 搜索字段存在或者是数组或者是json数据串，模糊查询
        $whereOr = [];
        if ($searchlist !== false && (is_array($searchlist) || strstr($searchlist, '{'))) {
            $searchlist = !is_array($searchlist) ? json_decode($searchlist, true) : $searchlist;
            foreach ($searchlist as $k => $v){
                $key = $tableName.$k;
                $whereOr[] = " {$key} like '%{$v}%' ";
            }
            $whereOr = implode('or', $whereOr);
        }

        return [$where,$field ,$limit, $order, $sort, $whereOr];
    }

    /**
     * 设置模型
     * @return $this 返回模型对象
     */
    public function setModel($model = null)
    {
        if (!empty($model)) {
            $this->model = $model;
        }

        return $this;
    }

    /**
     * 获取模型
     * @return Object 返回模型对象
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 将数组2合并进数组1的数据
     * @param $data1 array 二维数组1
     * @param $data2 array 二维数组2
     * @param $relation1 string 数组1关联字段名
     * @param $relation2Name string 数组2合并后命名
     */
    public function mergeData($data1, $data2, $relation1, $relation2Name)
    {
        foreach ($data1 as $k => $v) {
            !empty($data2[$v[$relation1]]) && $data1[$k][$relation2Name] = $data2[$v[$relation1]];
        }

        return $data1;
    }

    /**
     * 返回错误信息
     */
    public function getError()
    {
       return $this->error;
    }

    /**
     * 获取返回的数据
     * @return array|string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取返回的状态码
     * @return array|string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 启动事务
     * @param bool $startTrans 事务状态
     * @return $this 当前对象
     */
    public function startTrans($startTrans = true)
    {
        if ($startTrans && !self::$startTrans) {
            Db::startTrans();
            self::$startTrans = true;
        }
        return $this;
    }

    /**
     * 启动事务锁
     * @param bool $lock 查询事务锁状态
     * @return $this
     */
    public function lock($lock = false)
    {
        $lock && $this->lock = true;
        return $this;
    }

    /**
     * 启动验证类
     * @param bool $lock 验证状态
     * @return $this
     */
    public function validate($validate = true)
    {
        $this->validate = $validate;
        return $this;
    }

    /**
     * 选用验证场景
     * @param bool $lock 验证状态
     * @return $this
     */
    public function scene($scene = true)
    {
        $this->scene = $scene;
        return $this;
    }

    /**
     * 是否过滤不存在字段
     * @param bool $lock 验证状态
     * @return $this
     */
    public function allowField($allowField = true)
    {
        $this->allowField = $allowField;
        return $this;
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        if (self::$startTrans) {
            Db::rollback();
            self::$startTrans = false;
        }
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        if (self::$startTrans) {
            Db::commit();
            self::$startTrans = false;
        }

        return $this;
    }

    /**
     * 设置错误信息
     * @param string $error 错误信息
     * @return bool false
     */
    protected function setError($error = NULL, $code = 0)
    {
        $this->code = $code;
        !empty($error) && $this->error = $error ?? __('ERROR');
        return false;
    }

    /**
     * 设置错误信息
     * @param string $error 错误信息
     * @return bool false
     */
    public function returnError($error = NULL)
    {
        $this->rollback();
        $this->error($error);
    }

    /**
     * 设置成功返回状态
     * @param array|string $data 数据
     * @param int $code 消息码
     * @return bool
     */
    protected function returnSuccess($data = NULL, $code = 1)
    {
        $this->code = $code;
        !empty($data) && $this->data = $data;
        return true;
    }

    /**
     * 操作失败返回的数据
     * @param string $msg   提示信息
     * @param mixed $data   要返回的数据
     * @param int   $code   错误码，默认为0
     * @param string $type  输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作成功返回的数据
     * @param string $msg   提示信息
     * @param mixed $data   要返回的数据
     * @param int   $code   错误码，默认为1
     * @param string $type  输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        if (isset($header['statuscode']))
        {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        }
        else
        {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        ob_clean();
        throw new HttpResponseException($response);
    }

    /**
     * 调用不存在的静态方法
     * @param $method string 调用的方法名
     * @param $args array 调用的参数集合
     * @return mixed 返回调用的方法
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func([new Static(), $method], ...$args);
    }

    /**
     * 调用不存在的方法
     * @param string $name 方法名
     * @param array $arguments 参数变量名
     * @return object mixed 当前模型对象
     */
    public function __call($name, $arguments = [])
    {
        $this->model->$name(...$arguments);
        return $this;
    }
}