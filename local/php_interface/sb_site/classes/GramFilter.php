<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 08.12.2016
 * Time: 15:27
 */

namespace SB;

class GramFilter
{
    static $WEIGHT = [
        'VALUES' => [
            [
                "VALUE"            => 25,
                "HTML_VALUE_ALT"   => 25,
                "CONTROL_NAME_ALT" => '_WEIGHT_SB',
                "CONTROL_ID"       => 'WEIGHT_SB_25'
            ],
            [
                "VALUE"            => 50,
                "HTML_VALUE_ALT"   => 50,
                "CONTROL_NAME_ALT" => '_WEIGHT_SB',
                "CONTROL_ID"       => 'WEIGHT_SB_50'
            ],
            [
                "VALUE"            => 100,
                "HTML_VALUE_ALT"   => 100,
                "CONTROL_NAME_ALT" => '_WEIGHT_SB',
                "CONTROL_ID"       => 'WEIGHT_SB_100'
            ]
        ]
    ];

    static $weightChecked = 0;

    static $WEIGHT_D = [
        'VALUES' => [
            [
                "VALUE"            => 25,
                "HTML_VALUE_ALT"   => 25,
                "CONTROL_NAME_ALT" => '_WEIGHT_D_SB',
                "CONTROL_ID"       => 'WEIGHT_D_SB_25'
            ],
            [
                "VALUE"            => 50,
                "HTML_VALUE_ALT"   => 50,
                "CONTROL_NAME_ALT" => '_WEIGHT_D_SB',
                "CONTROL_ID"       => 'WEIGHT_D_SB_50'
            ],
            [
                "VALUE"            => 100,
                "HTML_VALUE_ALT"   => 100,
                "CONTROL_NAME_ALT" => '_WEIGHT_D_SB',
                "CONTROL_ID"       => 'WEIGHT_D_SB_100'
            ]
        ]
    ];

    static $weightDChecked = 0;
}