<?php

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

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Mittwald Matomo Widget',
  'description' => 'Dashboard widget that displays the visitor data of your Matomo instance',
  'category' => 'be',
  'author' => 'Mittwald CM Service GmbH',
  'author_email' => 'support@mittwald.de',
  'author_company' => 'Mittwald CM Service GmbH',
  'state' => 'beta',
  'clearCacheOnLoad' => true,
  'version' => '1.0.0',
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'Mittwald\\MatomoWidget\\' => 'Classes',
    ),
  ),
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '10.3.0-10.4.99',
      'php' => '7.2.0-7.4.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'uploadfolder' => false,
  'clearcacheonload' => true,
);

