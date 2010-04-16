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
	 * Abstract base class for file interfaces. This class contains basic file system
	 * access logic and defines the interfaces to retrieve files from a variety of
	 * remote sources.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_FileInterface
	 * @version    $Id$
	 *
	 */

Abstract Class Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The path to the local TYPO3 installation.
		 * @var string
		 */
	Protected $typo3path = PATH_site;





		/*
		 * ABSTRACT INTERFACE DEFINITIONS
		 */





		/**
		 *
		 * Retrieve a file from any location to a local directory.
		 *
		 * @param  string $source      The source filename
		 * @param  string $destination The target filename
		 * @return void
		 *
		 */

	Abstract Public Function retrieveFile($source, $destination);





		/*
		 * HELPER METHODS
		 */





		/**
		 *
		 * Recursively creates the parent directory of a file.
		 * @param  string $fileName The filename
		 * @return void
		 *
		 */

	Protected Function createParentDirectory($fileName) {
		$dirName = dirname($fileName);
		mkdir($dirName, 0777, TRUE);
	}

}

?>