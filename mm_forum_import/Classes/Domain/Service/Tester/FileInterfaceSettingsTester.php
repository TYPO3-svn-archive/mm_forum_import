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
	 * Tests the file system interface that was specified for import.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Tester
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Tester_FileInterfaceSettingsTester
	Extends Tx_MmForumImport_Domain_Service_Tester_AbstractTester {





		/*
		 * TEST METHODS
		 */





		/**
		 *
		 * Tests if there was any file system access specified at all.
		 * @return void
		 *
		 */

	Protected Function testIsSet() {

		If($this->importConfiguration->getFileinterfaceSettings() === NULL) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.notset', Array ( ),
													 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("fileinterface.test.exception");
		}

	}



		/**
		 *
		 * Tests if the local path that was specified as import source exists.
		 * @return void
		 *
		 */

	Protected Function testLocalFolder() {

		$directory = $this->importConfiguration->getFileinterfaceSettings()->getLocalPath();
		If($directory == '/') {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.localpath.empty', Array ( $directory ),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("fileinterface.test.exception");
		} ElseIf(!is_dir($directory)) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.localpath.failed', Array ( $directory ),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("fileinterface.test.exception");
		} Else {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.localpath', Array ( $directory ),
													 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_SUCCESS );
		}

	}



		/**
		 *
		 * Tests if the FTP connection that was specified as import source can be
		 * established.
		 * @return void
		 *
		 */

	Protected Function testFtpConnection() {

		Try {
			$settings  =& $this->importConfiguration->getFileinterfaceSettings();
			$interface =  New Tx_MmForumImport_Domain_Service_FileInterface_FTPFileInterface (
				$settings->getFtpHostname(), $settings->getFtpUsername(), $settings->getFtpPassword(), $settings->getFtpPath() );

			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.ftp',
													 Array ( $settings->getFtpHostname(), $settings->getFtpUsername() ),
													 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_SUCCESS );
		} Catch (Exception $e) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'fileinterface.test.ftp.failed',
													 Array ( $settings->getFtpHostname(), $settings->getFtpUsername() ),
													 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("fileinterface.test.exception");
		}

	}





		/*
		 * META METHODS
		 */





		/**
		 *
		 * Gets a list of tests to be performed.
		 * @return array A list of tests to be performed
		 *
		 */

	Protected Function getTestList() {
		$testList = Array();
		If($this->importConfiguration->getImportSource()->getDoesRequireFileImport()) {
			If($this->importConfiguration->getFileinterfaceSettings() !== NULL) {
				If($this->importConfiguration->getFileinterfaceSettings()->getIsFtp())
					array_push($testList, 'ftpConnection');
				ElseIf($this->importConfiguration->getFileinterfaceSettings()->getIsLocal())
					array_push($testList, 'localFolder');
			} Else array_push($testList, 'isSet');
		}
		Return $testList;
	}

}

?>