<?php

defined('TYPO3') or die();

call_user_func(static function () {
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mw_matomo_widget'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mw_matomo_widget'] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
            'options' => [
                'defaultLifetime' => 14400,
            ],
        ];
    }
});
