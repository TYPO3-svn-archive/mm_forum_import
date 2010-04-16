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
	 * Abstract configuration testing class.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Tester
	 * @version    $Id$
	 *
	 */

Abstract Class Tx_MmForumImport_Domain_Service_Tester_AbstractTester {





		/*
		 * CONSTANTS
		 */





		/**
		 * Class name that is to be used for warning messages.
		 */
	Const CLASS_WARNING = 'Tx_MmForumImport_Domain_Model_TestWarning';





		/*
		 * ATTRIBUTES
		 */





		/**
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration
		 */
	Protected $importConfiguration;

		/**
		 * @var Array<Tx_MmForumImport_Domain_Model_TestWarning>
		 */
	Protected $log;

		/**
		 * @var Integer
		 */
	Protected $exitStatus = 0;





		/*
		 * CONSTRUCTOR
		 */





		/**
		 *
		 * Creates a new testing class. The import configuration is injected as a
		 * parameter.
		 *
		 * @param Tx_MmForumImport_Domain_Model_ImportConfiguration $importConfiguration
		 *     The import configuration that is to be tested.
		 *
		 */
	Public Function __construct(Tx_MmForumImport_Domain_Model_ImportConfiguration $importConfiguration) {
		$this->importConfiguration = $importConfiguration;
	}





		/*
		 * META METHODS
		 */





		/**
		 *
		 * Performs a series of tests.
		 * @return boolean TRUE on success, otherwise FALSE.
		 *
		 */

	Public Function performTests() {
		$testList = $this->getTestList();

		ForEach($testList As $test) {
			$funcName = 'test'.ucfirst($test);
			If(is_callable(Array($this, $funcName))) {
				Try {
					$this->$funcName();
				} Catch (Exception $e) {
					$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING,
					                                         $e->getMessage(), Array(),
					                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_FATAL );
					Return $this->exitStatus = FALSE;
				}
			}
		}

		Return $this->exitStatus = TRUE;
	}



		/**
		 *
		 * Gets the exit status of the tester class. TRUE on success, otherwise FALSE.
		 * @return boolean The exit status of the tester class. TRUE on success,
		 *                 otherwise FALSE.
		 *
		 */

	Public Function getExitStatus() { Return $this->exitStatus; }



		/**
		 *
		 * Gets the log message stack.
		 * @return array The log message stack.
		 *
		 */

	Public Function getLogMessages() { Return $this->log; }



		/**
		 *
		 * Gets a list of tests to be performed.
		 * @return array.
		 *
		 */

	Abstract Protected Function getTestList();

}

?>