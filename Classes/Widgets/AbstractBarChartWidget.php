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

use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * The AbstractBarChartWidget class is a base class
 * for our Typo3 AbstractDoughnutChart widgets.
 */
class AbstractBarChartWidget extends BarChartWidget
{

    protected $extensionKey = 'mw_matomo_widget';
    const LANG_FILE = 'LLL:EXT:mw_matomo_widget/Resources/Private/Language/locallang.xlf';


    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var ChartDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var ButtonProviderInterface|null
     */
    private $buttonProvider;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        ChartDataProviderInterface $dataProvider,
        StandaloneView $view,
        $buttonProvider = null,
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->view = $view;
        $this->options = $options;
        $this->buttonProvider = $buttonProvider;
    }

    public function renderWidgetContent(): string
    {
        $path = ExtensionManagementUtility::extPath($this->extensionKey) . 'Resources/Private/';
        $this->view->getTemplatePaths()->setTemplateRootPaths([$path . 'Templates/']);
        $this->view->setTemplate('Widget/ChartWidget');
        $this->view->assignMultiple([
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'configuration' => $this->configuration,
            'widgetEnabled' => true,
        ]);
        return $this->view->render();
    }

    public function getEventData(): array
    {
        return [
            'graphConfig' => [
                'type' => 'bar',
                'options' => [
                    'maintainAspectRatio' => false,
                    'legend' => [
                        'display' => false,
                    ],

                ],
                'data' => $this->dataProvider->getChartData(),
            ],
        ];
    }

    public function getCssFiles(): array
    {
        return ['EXT:dashboard/Resources/Public/Css/Contrib/chart.css'];
    }

    public function getRequireJsModules(): array
    {
        return [
            'TYPO3/CMS/Dashboard/Contrib/chartjs',
            'TYPO3/CMS/Dashboard/ChartInitializer',
        ];
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
