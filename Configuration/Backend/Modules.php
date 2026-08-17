<?php

use SyntaxOOps\PluploadBE\Controller\UploadAjaxController;
use SyntaxOOps\PluploadBE\Controller\UploadController;
use TYPO3\CMS\Core\Information\Typo3Version;

$typo3Version = new Typo3Version();

return [
    'Plupload_BE' => [
        'parent' => $typo3Version->getMajorVersion() >= 14 ? 'media' : 'file',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/file/PluploadBE/',
        'appearance' => [
            'renderInModuleMenu' => false,
        ],
        'extensionName' => 'pluploadbe',
        'controllerActions' => [
            UploadController::class => [
                'index',
                'upload',
            ],
            UploadAjaxController::class => [
                'index',
                'upload',
            ],
        ],
    ],
];
