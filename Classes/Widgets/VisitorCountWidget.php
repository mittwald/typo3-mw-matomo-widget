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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Mittwald\MatomoWidget\Widgets\AbstractExtensionsWidget;

/**
 * The VisitorCountWidget receives Matomo data and
 * displays the site visitors from a given Matomo instance.
 */
class VisitorCountWidget extends AbstractExtensionsWidget
{
    protected $type = "visitors";
    protected $title = AbstractExtensionsWidget::LANG_FILE . ':visitorCountWidget.title';
    protected $description = AbstractExtensionsWidget::LANG_FILE . ':visitorCountWidget.description';

    /**
     * Checks if title should be for relative values
     */
    private function checkTitleIsRelative(): void
    {
        $backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get($this->extensionKey);
        $absoluteValues = $backendConfiguration["absoluteValues"];
        if (!$absoluteValues) {
            $this->title = \Mittwald\MatomoWidget\Widgets\AbstractExtensionsWidget::LANG_FILE . ':visitorCountWidget.title.relative';
        }
    }

    /**
     * Renders the widget content
     * @return string
     */
    public function renderWidget(): string
    {
        $this->checkTitleIsRelative();
        $this->loadData();
        return AbstractExtensionsWidget::renderWidget();
    }

    protected function prepareChartData(): void
    {
        $this->loadData();
        $this->chartData = [
            'labels' => [
                "letzte 24 Stunden",
                "letzten 7 Tage",
                "letzten 30 Tage"
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
