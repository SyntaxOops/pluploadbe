<?php

use SyntaxOOps\PluploadBE\Controller\UploadAjaxController;
use SyntaxOOps\PluploadBE\Controller\UploadController;

return [
    'Plupload_BE' => [
        'parent' => 'file',
        'position' => 'hidden',
        'access' => 'user,group',
        'workspaces' => 'live',
        'path' => '/module/file/PluploadBE/',
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
