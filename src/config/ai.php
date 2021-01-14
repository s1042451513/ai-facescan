<?php

// ai配置参数
return [
    // SkinRun肌肤管家配置
    'skinrun' => [
        // apptoken
        'app_token' => 'C6EB8E46595A45D98769A62298F29C5F',
        // app秘钥
        'app_secret' => 'b9ace95ad454efff300729e6ce806060',
        //根地址
        'api_base_url' => 'https://api.skinrun.cn/',
        // 在线测肤api
        'facescan_api_url' => 'face',
    ],
    // 宜远配置
    'yiyuan' => [
        // appid
        'app_id' => '3cafaf8fb29dfd88xx',
        // app秘钥
        'app_secret' => 'abe03226abc4a186fe188f5ec6b8b1bc',
        // 根地址
        'api_base_url' => 'https://api.yimei.ai/',
        // 在线测肤api
        'facescan_api_url' => 'v2/api/face/analysis/511',
    ],
    // ai在线测肤功能
    'facescan' => [
        // 驱动地址
        'driver_namespace' => '\\Johnson\\AiFacescan\\common\\driver\\facescan\\',
        // 驱动名/类型
        'driver_type' => 'skinrun',
        // 默认驱动类
        'default_class' => 'Request'
    ]
];