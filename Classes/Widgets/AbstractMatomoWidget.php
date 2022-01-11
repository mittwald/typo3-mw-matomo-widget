<?php

declare(strict_types=1);

/****************************************************************
 *  Copyright notice
 *
 *  (C) Mittwald CM Service GmbH & Co. KG <opensource@mittwald.de>
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
 ***************************************************************/

namespace Mittwald\MatomoWidget\Widgets;

use Mittwald\MatomoWidget\Service\MatomoService;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

abstract class AbstractMatomoWidget implements ChartDataProviderInterface
{
    private const LLL = 'LLL:EXT:mw_matomo_widget/Resources/Private/Language/locallang.xlf:';

    protected MatomoService $matomoService;

    public function __construct(MatomoService $matomoService)
    {
        $this->matomoService = $matomoService;
    }

    public function getChartData(): array
    {
        return [
            'labels' => [
                '24 ' . $this->getLanguageService()->sL(self::LLL . 'label.hours'),
                '7 ' . $this->getLanguageService()->sL(self::LLL . 'label.days'),
                '30 ' . $this->getLanguageService()->sL(self::LLL . 'label.days'),
            ],
            'datasets' => [
                [
                    'backgroundColor' => ['#FF8700', '#1A568F', '#4C7E3A'],
                    'data' => $this->getData(),
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        return [];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
