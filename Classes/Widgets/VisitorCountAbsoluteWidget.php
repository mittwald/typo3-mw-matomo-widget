<?php
declare(strict_types=1);
namespace Mittwald\MatomoWidget\Widgets;

/* * *************************************************************
 *  Copyright notice
 *
 *  (C) 2020 Mittwald CM Service GmbH & Co. KG <opensource@mittwald.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
/**
 * The VisitorCountWidget reads and displays the storage usage
 * of PHP OpCache module
 */
class VisitorCountAbsoluteWidget implements ChartDataProviderInterface
{
    private const decimals = 2; // decimals of graph values
    protected $extensionKey = 'mw_matomo_widget';
    protected $lifeTime = 3600;
    const LANG_FILE = 'LLL:EXT:mw_matomo_widget/Resources/Private/Language/locallang.xlf';
    protected $title = LANG_FILE . ':visitorCountWidget.title.relative';
    /**
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cache;

    /**
     * PageViewWidget constructor.
     */
    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->extensionKey);
    }

    /**
     * Load data from apcu extension
     */
    protected function loadData(): void
    {
        $cacheHash = md5($this->extensionKey);
        if ($this->items = $this->cache->get($cacheHash)) {
            return;
        }
        $backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get($this->extensionKey);

        $baseurl = $backendConfiguration["matomoUrl"];
        if (!preg_match('/^(http:\/\/)|^(https:\/\/)/m', $baseurl)) {
            // no protocol given
            $baseurl = 'http://' . $baseurl;
        }
        // Remove protocol prefix
        $absoluteValues = true;

        $siteId = $backendConfiguration["matomoSiteId"];
        $apitoken = $backendConfiguration["matomoToken"];

        $apiEndpoint30days = $baseurl . '/index.php?module=API&method=Live.getCounters&idSite=' . $siteId . '&lastMinutes=43200&format=JSON&token_auth=' . $apitoken;
        $apiEndpoint7days = $baseurl . '/index.php?module=API&method=Live.getCounters&idSite=' . $siteId . '&lastMinutes=10080&format=JSON&token_auth=' . $apitoken;
        $apiEndpoint1day = $baseurl . '/index.php?module=API&method=Live.getCounters&idSite=' . $siteId . '&lastMinutes=1440&format=JSON&token_auth=' . $apitoken;

        $content30days = GeneralUtility::getUrl($apiEndpoint30days);
        $content7days = GeneralUtility::getUrl($apiEndpoint7days);
        $content1day = GeneralUtility::getUrl($apiEndpoint1day);
        if ($content30days === false || $content1day === false || $content7days === false || !$this->isJson($content30days) || !$this->isJson($content1day) || !$this->isJson($content7days)) {
            return;
        }

        if ($absoluteValues) {
            $this->visitors30days = json_decode($content30days)[0]->visitors;
            $this->visitors7days = json_decode($content7days)[0]->visitors;
        } else {
            $this->visitors30days = number_format(json_decode($content30days)[0]->visitors / 30, 2);
            $this->visitors7days = number_format(json_decode($content7days)[0]->visitors / 7, 2);
        }
        $this->visitors1day = json_decode($content1day)[0]->visitors;

        $this->cache->set($cacheHash, $this->items, [$this->extensionKey], $this->lifeTime);
    }

    /**
     * @param string $string
     * @return bool if string is in json syntax
     */
    private function isJson(string $string): bool
    {
        @json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /*
    * returns the chart data
    */
    public function getChartData(): array
    {
        $this->loadData();
        return [
            'labels' => [
                "24 Stunden",
                "7 Tage",
                "30 Tage"
            ],
            'datasets' => [
                [
                    'backgroundColor' => ["#FF8700", "#1A568F", "#4C7E3A"],
                    'data' => [$this->visitors1day, $this->visitors7days, $this->visitors30days]
                ]
            ],
        ];
    }
}
