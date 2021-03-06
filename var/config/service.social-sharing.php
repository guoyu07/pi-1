<?php
// Social sharing specifications: title, icon, url

$config = [
    'items' => [
        'email'      => [
            'title' => __('Email'),
            'icon'  => 'fa-at',
            'url'   => 'mailto:?subject=%title%;body=%url%',
        ],
        'facebook'   => [
            'title' => __('Facebook'),
            'icon'  => 'fa-facebook',
            'url'   => 'https://www.facebook.com/sharer/sharer.php?u=%url%',
        ],
        'twitter'    => [
            'title' => __('Twitter'),
            'icon'  => 'fa-twitter',
            'url'   => 'http://www.twitter.com/home?status=%title%%url%',
        ],
        'tumblr'     => [
            'title' => __('Tumblr'),
            'icon'  => 'fa-tumblr',
            'url'   => 'http://tumblr.com/share?s=&amp;v=3&t=%title%&amp;u=%url%',
        ],
        'linkedin'   => [
            'title' => __('LinkedIn'),
            'icon'  => 'fa-linkedin',
            'url'   => 'http://www.linkedin.com/shareArticle?mini=true&amp;url=%url%&amp;title=%title%&amp;summary=%title%',
        ],
        'googleplus' => [
            'title' => __('Google+'),
            'icon'  => 'fa-google-plus',
            'url'   => 'https://plus.google.com/share?url=%title%%url%',
        ],
        'pinterest'  => [
            'title' => __('Pinterest'),
            'icon'  => 'fa-pinterest',
            'url'   => 'http://www.pinterest.com/pin/create/button/?url=%url%&amp;media=%image%&amp;description=%title%',
        ],
    ],
];

return $config;
