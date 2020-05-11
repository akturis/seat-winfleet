<?php

return [
    'winfleet' => [
        'name' => 'winfleet::winfleet.label',
        'label' => 'winfleet::winfleet.label',
        'icon' => 'fa-trophy',
        'route_segment' => 'winfleet',
        'route'         => 'winfleet.view',
        'permission' => 'winfleet.view',
        'entries' => [
            'winfleet' => [
                'name' => 'winfleet::winfleet.label',
                'label' => 'winfleet::winfleet.label',
                'icon' => 'fa-trophy',
                'route' => 'winfleet.view',
                'permission' => 'winfleet.view',
            ],
            'settings' => [
                'name' => 'winfleet::winfleet.settings',
                'label' => 'winfleet::winfleet.settings',
                'icon' => 'fa-cogs',
                'route' => 'winfleet.settings',
                'permission' => 'winfleet.settings',
            ],
        ],
    ],
];
