<?php

return [
    'class_url' => '',
    'info_url' => '',

    // Cache lifetime.
    'cache_minutes' => 7200,
    // 是否刷新缓存.
    'refresh_cache' => 0,

    // Api service host.
    'api_base_url' => 'http://pcsoft.api.zz314.com/pcsoft',

    // Models alias map to class.
    'models' => [
        'content' => SuperView\Models\ContentModel::class,
        'category' => SuperView\Models\CategoryModel::class,
        'zt' => SuperView\Models\TopicModel::class,
        'utils' => SuperView\Models\UtilsModel::class,
        'custom' => SuperView\Models\CustomModel::class,
        'comment' => SuperView\Models\CommentModel::class,
        'banner' => SuperView\Models\BannerModel::class,
    ],

    'dals' => [
        'content' => SuperView\Dal\Api\Content::class,
        'category' => SuperView\Dal\Api\Category::class,
        'zt' => SuperView\Dal\Api\Topic::class,
        'utils' => SuperView\Dal\Api\Utils::class,
        'custom' => SuperView\Dal\Api\Custom::class,
        'comment' => SuperView\Dal\Api\Comment::class,
        'banner' => SuperView\Dal\Api\Banner::class,
    ],

    'pagination' => [
        'layout' => '',
        'total' => '',
        'previous' => '',
        'links' => '',
        'link_active' => '',
        'next' => '',
        'dots' => '',
    ],

    //新缓存规则部分是使用
    'type' => [
        'category' => ['category'],
        'soft' => ['soft', 'iossoft', 'iosgame', 'game', 'dnb', 'azsoft', 'azgame'],
        'news' => ['aznews', 'iosnews', 'softnews', 'kjnews', 'ylnews', 'gamenews', 'dnbnews', 'smsnews', 'news'],
        'zt' => ['zt'],
    ],

    //api设置的最小查询limit
    'limit' => 15,

];