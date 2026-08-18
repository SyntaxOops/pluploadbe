<?php

$EM_CONF['pluploadbe'] = [
    'title' => 'Plupload BE',
    'description' => 'A TYPO3 extension that enables backend users to upload large files exceeding the upload_max_filesize limit in PHP.',
    'category' => 'module',
    'author' => 'Haythem Daoud',
    'author_email' => 'hello@haythemdaoud.dev',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'version' => '14.0.3',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '13.0.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'causal/image_autoresize' => '2.4.3-2.99.99',
        ],
    ],
];
