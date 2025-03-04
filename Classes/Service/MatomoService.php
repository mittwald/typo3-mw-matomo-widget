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

namespace Mittwald\MatomoWidget\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MatomoService
{
    protected const EXT_KEY = 'mw_matomo_widget';
    protected const CACHE_LIFETIME = 3600;
    protected FrontendInterface $cache;
    protected array $extensionConfiguration = [];

    public function __construct(
        protected readonly RequestFactory $requestFactory
    ) {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache(self::EXT_KEY);
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXT_KEY);
    }

    public function getMatomoData(string $method, bool $absolute = false): array
    {
        $content30days = $this->matomoApiRequest($method, ['period' => 'day', 'date' => 'last30']);
        $content7days = $this->matomoApiRequest($method, ['period' => 'day', 'date' => 'last7']);
        $content1day = $this->matomoApiRequest($method, ['period' => 'day', 'date' => 'today']);

        if ($content1day === '' || $content7days === '' || $content30days === '') {
            return [0, 0, 0];
        }

        $actions30days = array_sum(array_values(json_decode($content30days, true)));
        $actions7days = array_sum(array_values(json_decode($content7days, true)));
        $actions1day = array_sum(array_values(json_decode($content1day, true)));
        if (!$absolute) {
            $actions30days = number_format($actions30days / 30, 2);
            $actions7days = number_format($actions7days / 7, 2);
        }

        return [$actions1day, $actions7days, $actions30days];
    }

    protected function matomoApiRequest(string $method, array $arguments = []): string
    {
        $siteId = $this->extensionConfiguration['matomoSiteId'] ?? 1;
        $apiToken = $this->extensionConfiguration['matomoToken'] ?? '';
        $baseUrl = $this->getBaseUrl();

        $apiArguments = [
            'module' => 'API',
            'method' => $method,
            'idSite' => $siteId,
            'format' => 'JSON',
        ];

        $requestOptions = [
            'form_params' => [
                'token_auth' => $apiToken,
            ],
        ];

        $apiArguments += $arguments;
        $url = $baseUrl . '/index.php?' . http_build_query($apiArguments);
        $cacheHash = sha1($url);

        $result = $this->cache->get($cacheHash);
        if ($result === false || $result === null) {
            $response = $this->requestFactory->request($url, 'POST', $requestOptions);
            if ($response->getStatusCode() !== 200) {
                return '';
            }
            $result = (string)$response->getBody();

            if (!$this->isJson($result)) {
                return '';
            }
            $this->cache->set($cacheHash, $result, [], self::CACHE_LIFETIME);
        }

        return $result;
    }

    protected function getBaseUrl(): string
    {
        $baseurl = $this->extensionConfiguration['matomoUrl'] ?? '';
        if (!preg_match('/^(http:\/\/)|^(https:\/\/)/m', $baseurl)) {
            $baseurl = 'http://' . $baseurl;
        }

        return $baseurl;
    }

    protected function isJson(string $string): bool
    {
        @json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
