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
	 * Domain object that describes the configuration for a specific import process.
	 * This object contains information on the import source, the database connection
	 * that is to be used, settings on the local TYPO3 installation and so on...
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Model
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Model_ImportConfiguration
	Extends Tx_Extbase_DomainObject_AbstractValueObject {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The import source object.
		 * @var Tx_MmForumImport_Domain_Model_ImportSource
		 */
	Private $importSource = NULL;

		/**
		 * The database settings. This object contains the data needed to connect to the
		 * import source database.
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration_Database
		 */
	Private $databaseSettings = NULL;

		/**
		 * The file interface settings. This object contains all data that are necessary
		 * to access the import software's file system.
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface
		 */
	Private $fileinterfaceSettings = NULL;

		/**
		 * A list of all tables that are to be truncated before starting the import.
		 * @var array
		 */
	Private $truncateTables = Array();

		/**
		 * A list of all directories that are to be cleard before starting the import
		 * @var array
		 * @deprecated Is not used any more.
		 */
	Private $clearDirectories = Array();

		/**
		 * A settings array that is inherited from the calling controller object.
		 * @var Array
		 */
	Private $settings = Array();

		/**
		 * The charset used in the source software.
		 * @var string
		 */
	Private $sourceCharset = 'iso-8859-1';





		/*
		 * CONSTRUCTORS
		 */





		/**
		 *
		 * Creates a new import configuration.
		 *
		 * @param Tx_MmForumImport_Domain_Model_ImportSource $importSource
		 *     The import source software.
		 * @return void
		 *
		 */

	Public Function __construct(Tx_MmForumImport_Domain_Model_ImportSource $importSource) {
		$this->importSource = $importSource;
	}





		/*
		 * CONFIGURATION INJECTION
		 */





		/**
		 *
		 * Sets the connection data for the database that will be used as data source for
		 * the import process.
		 *
		 * @param  Array $databaseSettings An array containing database connection data.
		 * @return void
		 *
		 */

	Public Function injectDatabaseSettings(Array $databaseSettings) {
		$this->databaseSettings = Tx_MmForumImport_Domain_Model_ImportConfiguration_Database::createFromArray($databaseSettings);
	}



		/**
		 *
		 * Sets the tables to be truncated before importing.
		 * @param  array $truncateTables The tables to be truncated before importing.
		 * @return void
		 *
		 */

	Public Function injectTruncateTables(Array $truncateTables) {
		$this->truncateTables = $truncateTables;
	}



		/**
		 *
		 * Sets the global settings.
		 * @param  array $settings The settings array.
		 * @return void
		 *
		 */

	Public Function injectSettings(Array $settings) {
		$this->settings = $settings;
	}



		/**
		 *
		 * Sets the settings for the file interface to the source software's file system.
		 * @param array $fileInterface Settings for the file interface.
		 * @return void
		 *
		 */

	Public Function injectFileinterfaceSettings(Array $fileInterface) {
		$this->fileinterfaceSettings = Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface::createFromArray($fileInterface);
	}



		/**
		 *
		 * Sets the list of directories to be cleared before installation.
		 * @param  array $clearDirectories The list of directories to be cleared before
		 *                                 installation.
		 * @return void
		 *
		 */

	Public Function injectClearDirectories(Array $clearDirectories) {
		$this->clearDirectories = $clearDirectories;
	}





		/*
		 * GETTER METHODS
		 */





		/**
		 *
		 * Gets the import source software.
		 * @return Tx_MmForumImport_Domain_Model_ImportSource The import source software.
		 *
		 */

	Public Function getImportSource() { Return $this->importSource; }



		/**
		 *
		 * Gets the database connection data.
		 * @return Tx_MmForumImport_Domain_Model_DatabaseSettings The database connection
		 *                                                        data.
		 *
		 */

	Public Function getDatabaseSettings() { Return $this->databaseSettings; }



		/**
		 *
		 * Gets the connection data for the source software's file system.
		 * @return Tx_MmForumImport_Domain_Model_ImportConfiguration_FileInterface
		 *     The connection data for the source software's file system.
		 *
		 */

	Public Function getFileinterfaceSettings() { Return $this->fileinterfaceSettings; }



		/**
		 *
		 * Gets a list of all database tables that are required for the import. If the
		 * source software requires a table name prefix (like e.g. 'phpbb_'), this will
		 * be substituted automatically.
		 *
		 * @return array A list of all database tables that are required for the import.
		 *
		 */
	
	Public Function getRequiredTableNames() {
		$requiredTableNames =& $this->getImportSource()->getRequiredTableNames();
		ForEach($requiredTableNames As &$requiredTableName)
			$requiredTableName = str_replace('###PREFIX###', $this->getDatabaseSettings()->getPrefix(), $requiredTableName);
		Return $requiredTableNames;
	}



		/**
		 *
		 * Gets a list of all tables that are to be truncated before import.
		 * @return array A list of all tables that are to be truncated before import.
		 *
		 */

	Public Function getTruncateTables() { Return $this->truncateTables; }



		/**
		 *
		 * Gets the page UID of the forum storage page.
		 * @return integer The page UID of the forum storage page
		 *
		 */

	Public Function getForumPid() { Return $this->settings['pids']['forum']; }



		/**
		 *
		 * Gets the page UID of the user storage page.
		 * @return integer The page UID of the user storage page
		 *
		 */
	
	Public Function getUserPid() { Return $this->settings['pids']['user']; }



		/**
		 *
		 * Gets an instance of the mm_forum parent object. Usually, this is an instance
		 * of the tx_mmforum_module1 class.
		 * @return tx_mmforum_module1 The mm_forum parent object.
		 *
		 */
	
	Public Function getParentObject() { Return $this->settings['parentObject']; }



		/**
		 *
		 * Gets the source charset.
		 * @return string The source charset.
		 *
		 */

	Public Function getSourceCharset() { Return $this->sourceCharset; }



		/**
		 *
		 * Gets the list of directories to be cleared before import.
		 * @return array The list of directories to be cleared before import
		 *
		 */

	Public Function getClearDirectories() { Return $this->clearDirectories; }

}

?>