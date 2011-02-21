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
	 * Contains configuration data for the file system access for the import process.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Model_ImportConfiguration
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface Extends Tx_Extbase_DomainObject_AbstractValueObject {





		/*
		 * CONSTANTS
		 */





		/**
		 * Local file import.
		 */
	Const TYPE_LOCAL = 'local';

		/*
		 * FTP file import
		 */
	Const TYPE_FTP   = 'ftp';





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The interface type, e.g. 'local' or 'ftp'.
		 * @var string
		 */
	Private $type;

		/**
		 * The local path.
		 * @var string
		 */
	Private $path;

		/**
		 * The FTP host
		 * @var string
		 */
	Private $ftpHost;

		/**
		 * The FTP username
		 * @var string
		 */
	Private $ftpUsername;

		/**
		 * The FTP password
		 * @var string
		 */
	Private $ftpPassword;

		/**
		 * The FTP path
		 * @var string
		 */
	Private $ftpPath;





		/*
		 * CONSTRUCTORS
		 */





		/**
		 *
		 * Creates a new configuration from a single array.
		 *
		 * @param array $fileInterfaceConfiguration An array containing the configuration
		 *                                          data.
		 * @return Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface
		 *     An instance of this class containing the configuration data from the
		 *     submitted array.
		 */

	Static Public Function createFromArray(Array $fileInterfaceConfiguration) {
		If(!$fileInterfaceConfiguration['mode']) Return NULL;
		Return New Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface (
			$fileInterfaceConfiguration['mode'],
			$fileInterfaceConfiguration['mode'] == self::TYPE_FTP
				? $fileInterfaceConfiguration['ftp']['path'] : $fileInterfaceConfiguration['local']['path'],
			$fileInterfaceConfiguration['ftp']['host'],
			$fileInterfaceConfiguration['ftp']['username'],
			$fileInterfaceConfiguration['ftp']['password'] );
	}



		/**
		 *
		 * Creates a new instance of this class.
		 *
		 * @param string $type     The connection type, e.g. 'local' or 'ftp'
		 * @param string $path     The file path
		 * @param string $hostname The hostname (optional)
		 * @param string $username The username (optional)
		 * @param string $password The password (optional)
		 *
		 */

	Public Function __construct($type, $path, $hostname=NULL, $username=NULL, $password=NULL) {
		Switch($type) {
			Case self::TYPE_LOCAL:
				$this->path = $path;
				If(substr($this->path, -1) != '/') $this->path .= '/';
				Break;
			Case self::TYPE_FTP:
				$this->ftpPath = $path;
				If(substr($this->ftpPath, -1) != '/') $this->ftpPath .= '/';
				$this->ftpHost = $hostname;
				$this->ftpUsername = $username;
				$this->ftpPassword = $password; Break;
		} $this->type = $type;
	}





		/*
		 * GETTER METHODS
		 */





		/**
		 *
		 * Gets the connection type (e.g. 'local' or 'ftp').
		 * @return string The connection type
		 *
		 */

	Public Function getType() { Return $this->type; }



		/**
		 *
		 * Determines whether a FTP connection is configured.
		 * @return boolean TRUE if a FTP connection is configured, otherwise FALSE.
		 *
		 */

	Public Function getIsFtp() { Return $this->type == self::TYPE_FTP; }



		/**
		 *
		 * Determines whether a local connection is configured.
		 * @return boolean TRUE, if a local connection is configured, otherwise FALSE.
		 *
		 */

	Public Function getIsLocal() { Return $this->type == self::TYPE_LOCAL; }



		/**
		 *
		 * Gets the local import path.
		 * @return string The local import path.
		 *
		 */

	Public Function getLocalPath() { Return $this->path; }



		/**
		 *
		 * Gets the FTP file path.
		 * @return string The FTP path
		 *
		 */

	Public Function getFtpPath() { Return $this->ftpPath; }



		/**
		 *
		 * Gets the FTP hostname.
		 * @return string The FTP hostname
		 *
		 */

	Public Function getFtpHostname() { Return $this->ftpHost; }



		/**
		 *
		 * Gets the FTP username.
		 * @return string The FTP username
		 *
		 */

	Public Function getFtpUsername() { Return $this->ftpUsername; }



		/**
		 *
		 * Gets the FTP password.
		 * @return string The FTP password.
		 *
		 */

	Public Function getFtpPassword() { Return $this->ftpPassword; }

}

?>