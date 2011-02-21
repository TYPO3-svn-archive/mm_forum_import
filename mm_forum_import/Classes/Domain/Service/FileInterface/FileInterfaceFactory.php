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
	 * Factory class for creating file interface classes.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_FileInterface
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_FileInterface_FileInterfaceFactory {


	
		/**
		 *
		 * Creates a file interface instance for a specific file interface
		 * configuration. This may either be a local file connection or an FTP
		 * connection (maybe we'll implement additional interfaces in the future,
		 * like SSH, HTTP or who-knows-what...?).
		 *
		 * @param  Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface $fileInterfaceConfiguration
		 *                             The file interface configuration
		 * @return Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface
		 *                             A file interface.
		 *
		 */

	Public Function createFileInterface(Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface $fileInterfaceConfiguration) {
		$fileInterface = NULL;
		Switch($fileInterfaceConfiguration->getType()) {
			Case Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface::TYPE_LOCAL:
				$fileInterface =
					New Tx_MmForumImport_Domain_Service_FileInterface_LocalFileInterface (
						$fileInterfaceConfiguration->getLocalPath() );
				Break;
			Case Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface::TYPE_FTP:
				$fileInterface = New Tx_MmForumImport_Domain_Service_FileInterface_FTPFileInterface (
					$fileInterfaceConfiguration->getFtpHostname(),
					$fileInterfaceConfiguration->getFtpUsername(),
					$fileInterfaceConfiguration->getFtpPassword(),
					$fileInterfaceConfiguration->getFtpPath() );
				Break;
		} Return $fileInterface;
	}

}

?>
