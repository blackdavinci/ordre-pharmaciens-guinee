<?php

return [

    // These CSS rules will be applied after the regular template CSS

    /*
        'css' => [
            '.button-content .button { background: red }',
        ],
    */

    'colors' => [

        'highlight' => '#004ca3',
        'button'    => '#004cad',

    ],

    'view' => [
        'senderName'  => 'Office National des Pharmaciens de Guinée',
        'reminder'    => null,
        'unsubscribe' => null,
        'address'     => null,

        'logo'        => [
//            'path'   => '%PUBLIC%/vendor/beautymail/assets/images/sunny/logo.png',
            'path'   => public_path('storage/logo.png'), // Fallback logo
            'width'  => '',
            'height' => '',
        ],

        'twitter'  => null,
        'facebook' => null,
        'flickr'   => null,
    ],

];
