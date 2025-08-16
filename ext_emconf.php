<?php

$EM_CONF['pluploadbe'] = [
    'title' => 'Plupload BE',
    'description' => 'A TYPO3 extension that enables backend users to upload large files exceeding the upload_max_filesize limit in PHP.',
    'category' => 'module',
    'author' => 'Haythem Daoud',
    'author_email' => 'haythemdaoud.x@gmail.com',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'version' => '13.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'causal/image_autoresize' => '2.4.3-2.99.99',
        ],
    ],
];
