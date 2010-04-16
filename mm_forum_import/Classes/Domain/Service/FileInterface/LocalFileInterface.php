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
	 * Interface to retrieve import files from a local file system. This is used, if the
	 * installation of the import source is on the same server that the local TYPO3
	 * installation.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_FileInterface
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_FileInterface_LocalFileInterface Extends Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The absolute path of the source installation.
		 * @var string
		 */
	Private $path = './';





		/*
		 * CONSTRUCTOR
		 */





		/**
		 *
		 * Creates an interface for local file system access.
		 * @param string $path The absolute path of the source installation
		 *
		 */

	Public Function __construct($path) {
		parent::__construct();
		$this->path = $path;
	}





		/*
		 * FILE INTERFACE METHODS
		 */





		/**
		 *
		 * Copies a file from the source installation into the local TYPO3 directory.
		 *
		 * @param  string $source      The source file
		 * @param  string $destination The target file
		 * @return void
		 *
		 */

	Public Function retrieveFile($source, $destination) {
		If(!file_exists($this->typo3path.$destination))
			Throw New Tx_MmForumImport_Domain_Service_FileInterface_FileNotFoundException ("The file {$this->path}{$source} does not exist!");
		$this->createParentDirectory($this->typo3path.$destination);
		copy($this->path.$source, $this->typo3path.$destination);
	}

}

?>