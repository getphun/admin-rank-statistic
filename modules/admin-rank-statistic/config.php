<?php
/**
 * admin-rank-statistic config file
 * @package admin-rank-statistic
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'admin-rank-statistic',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/admin-rank-statistic',
    '__files' => [
        'modules/admin-rank-statistic'       => [ 'install', 'remove', 'update' ],
        'theme/admin/statistic/rank'         => [ 'install', 'remove', 'update' ],
        'theme/admin/static/js/site-rank.js' => [ 'install', 'remove', 'update' ]
    ],
    '__dependencies' => [
        'admin'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'AdminRankStatistic\\Controller\\RankController'   => 'modules/admin-rank-statistic/controller/RankController.php',
            'AdminRankStatistic\\Library\\RankCounter'         => 'modules/admin-rank-statistic/library/RankCounter.php',
            'AdminRankStatistic\\Model\\SiteRank'              => 'modules/admin-rank-statistic/model/SiteRank.php'
        ],
        'files' => []
    ],
    
    '_routes' => [
        'admin' => [
            'adminSiteRank' => [
                'rule' => '/statistic/rank',
                'handler' => 'AdminRankStatistic\\Controller\\Rank::index'
            ],
            'adminSiteRankUpdate' => [
                'rule' => '/statistic/rank/update',
                'handler' => 'AdminRankStatistic\\Controller\\Rank::update'
            ]
        ]
    ],
    
    'admin' => [
        'menu' => [
            'statistic' => [
                'label'     => 'Statistic',
                'order'     => 2000,
                'icon'      => 'line-chart',
                'submenu'   => [
                    'ranking'   => [
                        'label'     => 'Ranking',
                        'perms'     => 'read_site_rank',
                        'target'    => 'adminSiteRank',
                        'order'     => 10
                    ]
                ]
            ]
        ]
    ],
    
    'formatter' => [
        'site_rank' => [
            'created' => 'date'
        ]
    ],
];