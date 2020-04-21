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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AbstractBarChartWidget;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * The AbstractExtensionsWidget class is a base class
 * for our Typo3 AbstractBarChart widgets.
 */
class AbstractExtensionsWidget extends AbstractBarChartWidget
{
    const LANG_FILE = 'LLL:EXT:mw_matomo_widget/Resources/Private/Language/locallang.xlf';

    protected $extensionKey = 'mw_matomo_widget';
    protected $title = '';
    protected $description = '';
    protected $height = 4;
    protected $width = 2;
    protected $iconIdentifier = 'tx-mw_matomo_widget-widget-icon';
    protected $lifeTime = 3600;
    protected $chartOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'legend' => [
            'display' => false
        ],
        'scales' => [
            'yAxes' => [
                [
                    'ticks' => [
                        'beginAtZero' => true
                    ]
                ]
            ],
            'xAxes' => [
                [
                    'ticks' => [
                        'maxTicksLimit' => 15
                    ]
                ]
            ]
        ]
    ];

    /**
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cache;

    /**
     * AbstractExtensionsWidget constructor.
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        AbstractBarChartWidget::__construct($identifier);
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->extensionKey);
    }


    /**
     * Sets the chart data
     */
    protected function prepareChartData(): void
    {
        $this->loadData();
        $this->chartData = [
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
            ], 5
        ];
    }


    /**
     * Sets the view components
     */
    protected function initializeView(): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $path = ExtensionManagementUtility::extPath($this->extensionKey) . 'Resources/Private/';
        $this->view->getTemplatePaths()->setTemplateRootPaths([$path . 'Templates/']);
        $this->view->getTemplatePaths()->setLayoutRootPaths([$path . 'Layouts/']);
        $this->view->getTemplatePaths()->setPartialRootPaths([$path . 'Partials/']);
        $this->view->setTemplate('Widget/ChartWidget');
    }

    /**
     * Assigns the title and description to the view
     * @return string
     */
    public function renderWidget(): string
    {
        $this->view->assign('title', $this->getTitle());
        $this->view->assign('description', $this->getDescription());
        return $this->view->render();
    }

    /**
     * Gets the data from given Matomo API
     */
    protected function loadData(): void
    {
        $cacheHash = md5($this->identifier);
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
        $absoluteValues = $backendConfiguration["absoluteValues"];

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
            $this->actions30days = json_decode($content30days)[0]->actions;
            $this->actions7days = json_decode($content7days)[0]->actions;
        } else {
            $this->visitors30days = number_format(json_decode($content30days)[0]->visitors / 30, 2);
            $this->visitors7days = number_format(json_decode($content7days)[0]->visitors / 7, 2);
            $this->actions30days = number_format(json_decode($content30days)[0]->actions / 30, 2);
            $this->actions7days = number_format(json_decode($content7days)[0]->actions / 7, 2);
        }
        $this->visitors1day = json_decode($content1day)[0]->visitors;
        $this->actions1day = json_decode($content1day)[0]->actions;

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
}
