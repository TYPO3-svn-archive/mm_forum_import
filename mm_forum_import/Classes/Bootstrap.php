<?php

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2010 Martin Helmich <m.helmich@mittwald.de>                     *
 *           Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */



	/**
	 *
	 * Bootstrapping class for rendering the import module. This is necessary
	 * due to the changes in the Extbase API in version 1.3.0.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Controller
	 * @version    $Id$
	 *
	 */

Class tx_MmForumImport_Bootstrap Extends Tx_Extbase_Core_Bootstrap {



		/**
		 *
		 * Runs the mm_forum import module. This method basically does nothing
		 * else than calling the parent run method with modified parameters.
		 *
		 * @param  String $content       The plugin content. Can be emptry.
		 * @param  Array  $configuration The configuration.
		 * @return String                HTML content.
		 *
		 */

	Public Function run($content, $configuration) {
		$configuration['extensionName'] = 'MmForumImport';
		$configuration['pluginName'] = 'MmForumImportMmforum_MmForumImportM1';

		Return parent::run($content, $configuration);
	}

}

?>
