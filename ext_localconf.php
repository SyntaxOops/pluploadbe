<?php

defined('TYPO3') or die;

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Filelist\Controller\FileListController'] = [
        'className' => 'SyntaxOOps\PluploadBE\Xclass\FileListController',
    ];
})();
