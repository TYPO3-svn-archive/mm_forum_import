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
	 * Main controller class for the mm_forum data import module. This controller offers
	 * a step-by-step wizard to import data from a variety of platforms.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Controller
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Controller_MainController Extends Tx_Extbase_MVC_Controller_ActionController {





		/*
		 * CONSTANTS
		 */





		/**
		 * Class that shall be used to test the database settings.
		 */
	Const CLASS_TEST_DATABASE_SETTINGS = 'Tx_MmForumImport_Domain_Service_Tester_DatabaseSettingsTester';

		/**
		 * Class that shall be used to test the file interface settings.
		 */
	Const CLASS_TEST_FILEINTERFACE_SETTINGS = 'Tx_MmForumImport_Domain_Service_Tester_FileInterfaceSettingsTester';





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The import source repository.
		 * @var Tx_MmForumImport_Domain_Repository_ImportSourceRepository
		 */
	Protected $importSourceRepository = NULL;

		/**
		 * The current import configuration. This object will be created once when the
		 * import wizard is started, and then be persisted in the PHP session.
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration
		 */
	Protected $importConfiguration;





		/*
		 * ACTION METHODS
		 */





		 /**
		  *
		  * Initializes the controller. Creates an instance of the import source
		  * repository object and restores the current import configuration from the
		  * PHP session.
		  *
		  * @return void
		  *
		  */

	Protected Function initializeAction() {
		$this->importSourceRepository =& t3lib_div::makeInstance('Tx_MmForumImport_Domain_Repository_ImportSourceRepository');

		session_start();
		If($_SESSION['mm_forum_import']['configuration'])
			$this->importConfiguration =& $_SESSION['mm_forum_import']['configuration'];
	}



		/**
		 *
		 * The index action. This action presents a list of all available import sources.
		 * @return void
		 *
		 */

	Protected Function indexAction() {
		$this->importConfiguration = NULL;

		$allImportSources = $this->importSourceRepository->findAll();
		$this->view->assign("importSources", $allImportSources);
	}



		/**
		 *
		 * The data source configuration action. This action presents a form in which
		 * the user can specify the database connection data of the import source
		 * database.
		 *
		 * @param  string  $selectedSourceIdentifier The identifier of the software from
		 *                                           which the data is to be imported.
		 * @param  boolean $startSession             TRUE, to start a new session.
		 * @return void
		 *
		 */

	Protected Function dataSourceAction($selectedSourceIdentifier=NULL, $startSession=FALSE) {

		If($startSession) {
			$importSource = $this->importSourceRepository->findByUid($selectedSourceIdentifier);
			session_start();
			$this->importConfiguration = New Tx_MmForumImport_Domain_Model_ImportConfiguration($importSource);
			$this->importConfiguration->injectSettings($this->settings);
			$_SESSION['mm_forum_import']['configuration'] =& $this->importConfiguration;
		}

		$this->view->assign("configuration", $this->importConfiguration)
		           ->assign('pdoDrivers'   , $this->getDatabaseDrivers());
	}



		/**
		 *
		 * Tests the database connection data that were specified in the previous action.
		 * The user is given appropriate feedback, if something goes wrong.
		 *
		 * @param  array $database      The database connection data.
		 * @param  array $fileinterface The file interface parameters
		 * @return void
		 *
		 */

	Protected Function testDataSourceAction($database=NULL, $fileinterface=NULL) {

		If($database)      $this->importConfiguration->injectDatabaseSettings($database);
		If($fileinterface) $this->importConfiguration->injectFileinterfaceSettings($fileinterface);

		$databaseSettingsTester = t3lib_div::makeInstance(self::CLASS_TEST_DATABASE_SETTINGS, $this->importConfiguration);
		$databaseSettingsTester->performTests();

		$fileinterfaceSettingsTester = t3lib_div::makeInstance(self::CLASS_TEST_FILEINTERFACE_SETTINGS, $this->importConfiguration);
		$fileinterfaceSettingsTester->performTests();

		$this->view->assign('databaseTester'     , $databaseSettingsTester)
		           ->assign('fileinterfaceTester', $fileinterfaceSettingsTester)
		           ->assign('allExitStatus'      , $databaseSettingsTester->getExitStatus() && $fileinterfaceSettingsTester->getExitStatus())
		           ->assign('configuration'      , $this->importConfiguration);

	}



		/**
		 *
		 * Offers some additional configuration possibilities before the import is
		 * started. The configuration possibilities are dependent of the import source
		 * software.
		 *
		 * @return void
		 *
		 */

	Protected Function selectImportObjectsAction() {
		$this->view->assign('configuration', $this->importConfiguration);
	}



		/**
		 *
		 * Performs the actual import.
		 *
		 * @param  array $truncateTables Names of database tables that are to be
		 *                               truncated before the import.
		 * @return void
		 *
		 */

	Protected Function performImportAction(Array $truncateTables=Array()) {

		If(!Empty($truncateTables))   $this->importConfiguration->injectTruncateTables   ( $truncateTables   );

		$importer = $this->importConfiguration->getImportSource()->getImporterInstance();
		$importer->injectImportConfiguration($this->importConfiguration);
		$importer->startImport();

		$this->view->assign('importer', $importer)
		           ->assign('configuration', $this->importConfiguration);

	}



		/**
		 *
		 * Gets a list with all available PDO database drivers.
		 * @return Array A list with all available PDO database drivers.
		 *
		 */

	Protected Function getDatabaseDrivers() {
		$driverNames = Array ( 'mysql'    => 'MySQL',
		                       'pgsql'    => 'PostgreSQL',
		                       'sqlite'   => 'SQLite',
		                       'sqlite2'  => 'SQLite 2',
		                       'oci'      => 'Oracle',
		                       'sybase'   => 'MS SQL',
		                       'dblib'    => 'MS SQL',
		                       'mssql'    => 'MS SQL',
		                       'odbc'     => 'ODBC',
		                       'firebird' => 'Firebird',
		                       'ibm'      => 'IBM',
		                       'informix' => 'Informix' );
		$rawDrivers = PDO::getAvailableDrivers();
		$resultDrivers = Array();

		ForEach($rawDrivers As $driver)
			$resultDrivers[$driver] = $driverNames[$driver] ? $driverNames[$driver] : strtoupper($driver);
		Return $resultDrivers;
	}

}

?>