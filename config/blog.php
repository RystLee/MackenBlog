<?php

return [

    // Default Owner
    'default_owner' => env('DEFAULT_OWNER') ?: '麦肯先生',

    // Default MOTTO
    'default_motto' => env('DEFAULT_MOTTO') ?: 'Simplicity is the essence of happiness.',

    // Default Avatar
    'default_avatar' => env('DEFAULT_AVATAR') ?: '/images/default.png',

    // Default Icon
    'default_icon' => env('DEFAULT_ICON') ?: '/images/favicon.ico',

    // Social Share
    'social_share' => [
        'article_share'    => env('ARTICLE_SHARE') ?: true,
        'sites'            => env('SOCIAL_SHARE_SITES') ?: 'google,twitter,weibo',
        'mobile_sites'     => env('SOCIAL_SHARE_MOBILE_SITES') ?: 'google,twitter,weibo,qq,wechat',
    ],

    // Google Analytics
    'google' => [
        'id'   => env('GOOGLE_ANALYTICS_ID', 'Google-Analytics-ID'),
        'open' => env('GOOGLE_OPEN') ?: false
    ],

    // Article Page
    'article' => [
        'title'       => 'Nothing is impossible.',
        'description' => 'https://pigjian.com',
        'number'      => 10,
        'sort'        => 'desc',
        'sortColumn'  => 'published_at',
    ],

    // Discussion Page
    'discussion' => [
        'number' => 20,
        'sort'   => 'desc',
        'sortColumn' => 'created_at',
    ],

    // Footer
    'footer' => [
        'github' => [
            'open' => true,
            'url'  => 'https://github.com/jcc',
        ],
        'twitter' => [
            'open' => true,
            'url'  => 'https://twitter.com/pigjian'
        ],
        'meta' => '© PJ Blog 2016. Powered By Jiajian Chan',
    ],

    'license' => 'Powered By Jiajian Chan.<br/>This article is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">Creative Commons Attribution-NonCommercial 4.0 International License</a>.',

];
