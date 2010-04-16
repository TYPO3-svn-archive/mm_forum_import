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
	 * Tests the database connection that was specified for import.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Tester
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Tester_DatabaseSettingsTester
	Extends Tx_MmForumImport_Domain_Service_Tester_AbstractTester {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The database connection.
		 * @var PDO
		 */
	Private $databaseConnection = NULL;





		/*
		 * TEST METHODS
		 */





		/**
		 *
		 * Tests if there was any database connection specified at all.
		 * @return void
		 *
		 */

	Protected Function testIsSet() {

		If($this->importConfiguration->getDatabaseSettings() === NULL) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.notset', Array(),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("datasource.test.exception");
		}

	}



		/**
		 *
		 * Tests if the PDO extension and the selected driver exist.
		 * @return void
		 *
		 */

	Protected Function testDatabaseDriver() {

		$databaseSettings =& $this->importConfiguration->getDatabaseSettings();

		If(!class_exists('PDO')) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.pdo.exists.failed', Array(),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("datasource.test.exception");
		} Else $this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.pdo.exists' );

		If(in_array($databaseSettings->getDriver(), PDO::getAvailableDrivers())) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.pdo.driver', Array($databaseSettings->getDriver()) );
		} Else {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.pdo.driver.failed', Array($databaseSettings->getDriver()),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("datasource.test.exception");
		}

	}



		/**
		 *
		 * Tests if a connection to the specified database can be established.
		 * @return void
		 *
		 */

	Protected Function testDatabaseConnection() {

		$databaseSettings =& $this->importConfiguration->getDatabaseSettings();

		Try {
			$this->databaseConnection = New PDO ( $databaseSettings->getDSN(),
			                                      $databaseSettings->getUsername(),
			                                      $databaseSettings->getPassword() );
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.connection',
		                                             Array ( $databaseSettings->getHostname(),
		                                                     $databaseSettings->getUsername(),
			                                                 $databaseSettings->getDSN() ) );
		} Catch(PDOException $e) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.connection.failed',
			                                         Array ( $databaseSettings->getHostname(),
			                                                 $databaseSettings->getUsername(),
			                                                 $databaseSettings->getDSN(), $e->getMessage() ),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw New Exception("datasource.test.exception");
		}

	}



		/**
		 *
		 * Tests if the database tables that are required to be existing for import
		 * actually exist.
		 * @return void
		 *
		 */

	Protected Function testDatabaseTables() {

		$requiredTables = $this->importConfiguration->getRequiredTableNames();
		$existingTables = Array();

		Try {
			ForEach($this->databaseConnection->query('SHOW TABLES', PDO::FETCH_COLUMN, 0) As $tableName)
				$existingTables[] = $tableName;
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.databasetables.loaded',
			                                         Array ( count($existingTables) ),
			                                         Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_NOTICE );
		} Catch(PDOException $e) {
			$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.databasetables.queryError',
			                                         Array ( ), Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
			Throw $e;
		}

		ForEach($requiredTables As &$requiredTable) {
			If(in_array($requiredTable, $existingTables)) {
				$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.databasetables.tableexists',
														 Array ( $requiredTable ),
														 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_SUCCESS );
			} Else {
				$this->log[] = t3lib_div::makeInstance ( self::CLASS_WARNING, 'datasource.test.databasetables.tablenotexists',
														 Array ( $requiredTable ),
														 Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_WARNING );
				Throw New Exception("datasource.test.exception");
			}
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
		If($this->importConfiguration->getImportSource()->getDataSourceMode() === 'database') {
			array_push($testList, 'isSet');
			array_push($testList, 'databaseDriver');
			array_push($testList, 'databaseConnection');
			array_push($testList, 'databaseTables');
		} Return $testList;
	}

}

?>