<?php
return [
    'Push' => [
        'adapters' => [
            'Fcm' => [
                'api' => [
                    'key' => env('FCM_CHAVE_SERVIDOR'),
                    'url' => 'https://fcm.googleapis.com/fcm/send',
                ],
            ],
        ],
    ],
];
