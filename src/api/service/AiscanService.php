<?php

namespace Wjohnson\AiFacescan\api\service;

use Wjohnson\AiFacescan\common\service\BaseService;
use Wjohnson\AiFacescan\common\model\UserFacescan as ServiceModel;
use Wjohnson\AiFacescan\common\facade\ConfigFacade;
use think\Log;

/**
 * 文章列表服务模型类
 */
class AiscanService extends BaseService
{
    /**
     * @var Object Model 实例
     */
    protected $model = null;

    /**
     * 构造函数
     * ArticleService constructor.
     */
    public function __construct(){
        $this->model = new ServiceModel();
        parent::__construct();
    }

    /**
     * 面部扫描
     * @param int $userId 用户id
     * @param string $image 图片
     * @param string|int $age 年龄
     * @return bool|mixed
     */
    public function faceScan($userId, $image, $age = '')
    {
        // 生成配置参数
        $config =  ConfigFacade::getConfig('ai.facescan');
        $driver = $config['driver_namespace'].$config['driver_type'].'\\'.$config['default_class'];

        // 检测扫描结果存在则直接返回
        $checkExistsScanResult = $this->hasScanResult($userId, $image);
        if ($checkExistsScanResult) {
            return $checkExistsScanResult;
        }

        // 进行扫描
        $scanResult = $this->requestAiFaceScan($driver, $image, $age);
        Log::write(json_encode($scanResult), '---------------------------facescan------------------------');

        // 保存结果
        $resultData = $this->saveResult($userId, $config['driver_type'], $image, $scanResult);

        // 返回扫描结果
        return $resultData;
    }

    /**
     * 获取某段时间内肌肤分析的数据
     */
    public function getFacescanDataAndLast($userId, $startDate = '', $endDate = '')
    {
        // 参数初始化验证
        $todayDate = date('Y-m-d');
        if (empty($startDate) || $startDate > $todayDate) {
            $this->returnError(__('Data_exception'));
        }
        empty($endDate) && $endDate = $todayDate;

        // 获取某段时间的颜值得分
        $appearanceData = $this->select([
            'u_id' => $userId,
            'create_time' => ['between', strtotime($startDate.' 00:00:01').','.strtotime($endDate.' 23:59:59')],
        ], 'appearance,create_time');

        // 获取单条数据
        $lastData = $this->select([
            'u_id' => $userId,
        ], '*', '1', 'id desc');

        // 生成返回结果
        return [
            'appearance_data' => $appearanceData,
            'last_data' => $lastData[0]
        ];
    }

    /**
     * 查询扫描结果存在则返回
     */
    private function hasScanResult(&$userId, &$image)
    {
        $resultData = $this->find([
            'u_id' => $userId,
            'imgmd5' => $this->getSaveImgStr($image),
        ]);

        if (!empty($resultData)) {
            return $resultData;
        }

        return false;
    }

    /**
     * ai面部扫描识别
     */
    private function requestAiFaceScan($driver, &$image, $age = '')
    {
        // 验证类
        if (!class_exists($driver)) {
            $this->returnError('驱动名配置错误');
        }

        $driver = new $driver();
        $param = [
            'image' => $image,
            'age' => (int)$age
        ];
//        $result = $driver->scan($param);
        $result = "{\"code\":200,\"data\":{\"analyse\":{\"image\":\"https:\\\/\\\/skinrun-api.oss-cn-shanghai.aliyuncs.com\\\/face\\\/crop\\\/2021-01-14\\\/1610605932\\\/1507050110.jpg\",\"scale_three\":\"0.90:1.12:1.09\",\"scale_five\":\"1.22:1.15:1.62:1.15:1.08\",\"scale_gold\":\"66.01\",\"philtrum\":15,\"gold_points\":\"[[70,140],[147,140],[109,199]]\",\"three_courts_points\":\"[[113,60],[92,117],[94,195],[109,271]]\",\"five_eyes_points\":\"[[20,136],[54,140],[86,144],[131,144],[163,140],[193,140]]\"},\"skin_data\":[{\"skin\":1,\"level\":\"0.31\",\"score\":69,\"number\":0,\"label_img\":\"\",\"percent\":\"0.29\",\"special\":\"[]\"},{\"skin\":2,\"level\":\"0.34\",\"score\":66,\"number\":0,\"label_img\":\"\",\"percent\":\"0.15\",\"special\":\"[1,3]\"},{\"skin\":3,\"level\":\"0.00\",\"score\":100,\"number\":0,\"label_img\":\"https:\\\/\\\/skinrun-api.oss-cn-shanghai.aliyuncs.com\\\/face\\\/crop\\\/2021-01-14\\\/1610605932\\\/pigment.png\",\"percent\":\"0.30\",\"special\":\"[]\"},{\"skin\":4,\"level\":\"0.00\",\"score\":100,\"number\":0,\"label_img\":\"https:\\\/\\\/skinrun-api.oss-cn-shanghai.aliyuncs.com\\\/face\\\/crop\\\/2021-01-14\\\/1610605932\\\/ance.png\",\"percent\":\"0.40\",\"special\":\"[]\"},{\"skin\":5,\"level\":\"0.13\",\"score\":87,\"number\":1,\"label_img\":\"https:\\\/\\\/skinrun-api.oss-cn-shanghai.aliyuncs.com\\\/face\\\/crop\\\/2021-01-14\\\/1610605932\\\/eyeblack.png\",\"percent\":\"0.60\",\"special\":\"[{\\\"left\\\":[]},{\\\"right\\\":[]}]\"},{\"skin\":6,\"level\":\"0.24\",\"score\":76,\"number\":0,\"label_img\":\"\",\"percent\":\"0.00\",\"special\":\"[]\"},{\"skin\":7,\"level\":\"0.26\",\"score\":74,\"number\":0,\"label_img\":\"\",\"percent\":\"0.00\",\"special\":\"[]\"},{\"skin\":8,\"level\":\"0.36\",\"score\":64,\"number\":0,\"label_img\":\"\",\"percent\":\"0.66\",\"special\":\"[]\"},{\"skin\":9,\"level\":\"0.15\",\"score\":85,\"number\":4,\"label_img\":\"https:\\\/\\\/skinrun-api.oss-cn-shanghai.aliyuncs.com\\\/face\\\/crop\\\/2021-01-14\\\/1610605932\\\/blackhead.png\",\"percent\":\"0.00\",\"special\":\"[]\"},{\"skin\":10,\"level\":\"0.10\",\"score\":90,\"number\":0,\"label_img\":\"\",\"percent\":\"0.00\",\"special\":\"[]\"}],\"part_data\":[{\"part\":1,\"width\":173,\"height\":217,\"type\":3,\"points\":\"[[20,136],[22,159],[26,181],[31,203],[39,223],[54,240],[72,255],[90,267],[109,271],[128,266],[145,254],[161,240],[174,223],[183,204],[188,183],[191,162],[193,140],[62,54],[86,60],[113,60],[152,55],[169,61],[190,97],[31,83],[42,65],[21,120],[192,121],[177,73],[143,60]]\",\"ratio\":1},{\"part\":2,\"width\":57,\"height\":11,\"type\":4,\"points\":\"[[35,120],[47,111],[62,109],[78,111],[92,117]]\",\"ratio\":33},{\"part\":3,\"width\":32,\"height\":12,\"type\":2,\"points\":\"[[54,140],[64,134],[76,135],[86,144],[76,146],[64,145]]\",\"ratio\":57},{\"part\":4,\"width\":56,\"height\":30,\"type\":3,\"points\":\"[[77,217],[89,213],[101,211],[108,214],[116,211],[127,213],[138,217],[127,230],[117,236],[108,238],[100,236],[88,230],[77,217],[82,218],[100,219],[108,220],[116,218],[133,218],[138,217]]\",\"ratio\":0},{\"part\":6,\"width\":30,\"height\":60,\"type\":2,\"points\":\"[[109,139],[110,154],[110,170],[110,185],[94,195],[101,197],[109,199],[117,197],[124,194]]\",\"ratio\":1},{\"part\":5,\"width\":152,\"height\":68,\"type\":1,\"points\":\"[[31,203],[39,223],[54,240],[72,255],[90,267],[109,271],[128,266],[145,254],[161,240],[174,223],[183,204]]\",\"ratio\":120}],\"conclusion\":[{\"cid\":1,\"name\":\"\u7ec6\u817b\u5ea6\",\"score\":83},{\"cid\":2,\"name\":\"\u5300\u51c0\u5ea6\",\"score\":95},{\"cid\":3,\"name\":\"\u7d27\u81f4\u5ea6\",\"score\":66},{\"cid\":4,\"name\":\"\u8010\u53d7\u5ea6\",\"score\":64},{\"cid\":5,\"name\":\"\u6c34\u6cb9\u5e73\u8861\u5ea6\",\"score\":50}],\"skin_age\":29,\"skin_score\":81,\"skin_type\":2,\"dateline\":1610605936},\"message\":\"\u64cd\u4f5c\u6210\u529f\"}";
        $result = json_decode($result, true);
        $result = $result['data'];

        if (!$result) {
            $this->returnError($driver->getError());
        }
        return $result;
    }

    /**
     * 保存结果
     * @param int $userId 用户id
     * @param string $driverName 肌肤检测驱动名
     * @param string $img 图片
     * @param array $result 测试的数据结果
     * @return bool
     */
    private function saveResult($userId, $driverName, $img, &$result)
    {
        $buildParamFun = 'get'.ucfirst($driverName).'SaveData';
        $insertResult = $this->$buildParamFun($result);
        $insertResult['u_id'] = $userId;
        $insertResult['imgmd5'] = $this->getSaveImgStr($img);
        $insertResult['img'] = $img;
        try {
            $this->validate(false)->insert($insertResult);
        }catch (\PDOException $e) {
            $this->returnError($e->getMessage());
        }

        $resultData = $this->model->getData();
        $resultData['create_time'] = date('Y-m-d H:i:s', $resultData['create_time']);
        $resultData['update_time'] = date('Y-m-d H:i:s', $resultData['update_time']);
        return $resultData;
    }

    /**
     * 获取宜远保存参数
     * @param $scanResult
     * @return array
     */
    private function getYiyuanSaveData($scanResult)
    {
        return [];
    }

    /**
     * 获取肌肤管家保存参数
     * @param $scanResult
     * @return array
     */
    private function getSkinrunSaveData($resultData)
    {
        $skinData = array_column($resultData['skin_data'], 'score', 'skin');
        return [
            'age' => $resultData['skin_age'],
            'pockmark' => 0,
            'toily' => $skinData[7],
            'uoily' => $skinData[6],
            'spot' => $skinData[4],
            'wrinkle' => $skinData[2],
            'blackhead' => $skinData[9],
            'pore' => $skinData[1],
            'sensitive' => $skinData[8],
            'dark_circle' => $skinData[5],
            'appearance' => $resultData['skin_score'],
            'question' => '',
            'advise' => '',
        ];
    }

    /**
     * 获取保存到数据库的图片字符串
     */
    private function getSaveImgStr($img)
    {
        return md5($img);
    }

}