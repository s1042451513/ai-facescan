<?php

// ai配置参数
return [
    // SkinRun肌肤管家配置
    'skinrun' => [
        // apptoken
        'app_token' => '',
        // app秘钥
        'app_secret' => '',
        //根地址
        'api_base_url' => 'https://api.skinrun.cn/',
        // 在线测肤api
        'facescan_api_url' => 'face',
    ],
    // 宜远配置
    'yiyuan' => [
        // appid
        'app_id' => '',
        // app秘钥
        'app_secret' => '',
        // 根地址
        'api_base_url' => 'https://api.yimei.ai/',
        // 在线测肤api
        'facescan_api_url' => 'v2/api/face/analysis/511',
    ],
    // ai在线测肤功能
    'facescan' => [
        // 驱动地址
        'driver_namespace' => '\\Wjohnson\\AiFacescan\\common\\driver\\facescan\\',
        // 驱动名/类型
        'driver_type' => 'skinrun',
        // 默认驱动类
        'default_class' => 'Request'
    ]
];
