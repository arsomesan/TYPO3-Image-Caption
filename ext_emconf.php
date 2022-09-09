<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Image-Caption',
    'description' => 'Automatic Image Caption using Max Image Caption Generator',
    'category' => 'services',
    'author' => 'Adrian Somesan',
    'author_email' => '',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
