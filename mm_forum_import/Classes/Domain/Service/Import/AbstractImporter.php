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
	 * Abstract base class for importer services. This class may be used for the import
	 * from any source software and provides access to both the local TYPO3 database and
	 * the remote import source database and to the filesystem of both software
	 * installations.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import
	 * @version    $Id$
	 *
	 */

Abstract Class Tx_MmForumImport_Domain_Service_Import_AbstractImporter {





		/*
		 * ATTRIBUTES
		 */




	
		/**
		 * The remote database connection.
		 * @var PDO
		 */
	Protected $remoteDatabase;

		/**
		 * The local database connection.
		 * @var PDO
		 */
	Protected $localDatabase;

		/**
		 * The import configuration
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration
		 */
	Protected $importConfiguration;

		/**
		 * Warnings and other messages that are logged while importing
		 * @var array<Tx_MmForumImport_Domain_Model_Warning>
		 */
	Protected $log;

		/**
		 * The items that are to be imported.
		 * @var array
		 */
	Protected $importItems = Array();

		/**
		 * An array that maps the unique identifiers of existing object to newly created
		 * records.
		 * @var array
		 */
	Protected $uidMapping = Array();

		/**
		 * An interface to the file system of both the import source and the local TYPO3
		 * installation.
		 * @var Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface
		 */
	Protected $fileInterface = NULL;

		/**
		 * The database prefix. This can be access also by
		 * $this->importSource->getDatabaseSettings()->..., but is stored here for
		 * convenience.
		 * @var string
		 */
	Protected $prefix = '';





		/*
		 * CONSTRUCTORS
		 */





		/**
		 *
		 * Creates an instance of the importer. This methods establishes a connection to
		 * the local TYPO3 database using the connection data stored in the localconf.php.
		 *
		 */

	Public Function __construct() {
		$this->localDatabase = New PDO('mysql:host='.TYPO3_db_host.';dbname='.TYPO3_db, TYPO3_db_username, TYPO3_db_password);
		$this->localDatabase->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}





		/*
		 * PUBLIC METHODS
		 */





		/**
		 *
		 * Sets the import configuration. After injecting this object, the importer will
		 * automatically try to connect to the remote database and file system.
		 *
		 * @param Tx_MmForumImport_Domain_Model_ImportConfiguration $importConfiguration
		 *     The import configuration.
		 * @return void
		 *
		 */

	Public Function injectImportConfiguration(Tx_MmForumImport_Domain_Model_ImportConfiguration $importConfiguration) {
		$this->importConfiguration = $importConfiguration;
		$this->remoteDatabase = New PDO ( $importConfiguration->getDatabaseSettings()->getDSN(),
		                                  $importConfiguration->getDatabaseSettings()->getUsername(),
		                                  $importConfiguration->getDatabaseSettings()->getPassword() );
		$this->remoteDatabase->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->importItems = $importConfiguration->getImportSource()->getImportItems();

		$this->fileInterface = New Tx_MmForumImport_Domain_Service_FileInterface_LocalFileInterface('/var/www/phpbb/');

		$this->prefix = $this->importConfiguration->getDatabaseSettings()->getPrefix();
	}



		/**
		 *
		 * Starts the data import. This methods evaluates the "importItem" settings from
		 * the source software and instantiates specific aspect importer classes or calls
		 * the appropriate local methods.
		 *
		 * @return void
		 *
		 */

	Public Function startImport() {

		$this->truncateTables();

		ForEach($this->importItems As $importItem) {
			$className = get_class($this).'_'.ucfirst($importItem).'Importer';
			$funcName = 'import'.ucfirst($importItem);
			If(class_exists($className, TRUE)) {
				$aspectImporter = t3lib_div::makeInstance($className);
				$aspectImporter->injectLocalDatabaseConnection  ( $this->localDatabase       )
				               ->injectRemoteDatabaseConnection ( $this->remoteDatabase      )
				               ->injectUidMapping               ( $this->uidMapping          )
				               ->injectImportConfiguration      ( $this->importConfiguration )
				               ->injectFileInterface            ( $this->fileInterface       )
				               ->injectParentObject             ( $this                      );
			} Else {
				$aspectImporter = $this;
			}

			If(!is_callable(Array($aspectImporter, $funcName))) Throw New Exception("Method $funcName is not callable!", 1270818634);
			$aspectImporter->$funcName();
		}

		$this->resetCacheVariables();
	}





		/*
		 * GENERAL IMPORT METHODS
		 */





		/**
		 *
		 * Refills the internal cache attributes of the mm_forum tables. Each forum
		 * records contains e.g. a reference to the last post, or the total number of
		 * posts in this forum.
		 *
		 * @return void
		 *
		 */

	Protected Function resetCacheVariables() {

		$forums = "UPDATE tx_mmforum_forums f SET
		               forum_topics=(SELECT COUNT(1) FROM tx_mmforum_topics t WHERE t.forum_id = f.uid AND deleted=0),
					   forum_last_post_id=(SELECT uid FROM tx_mmforum_posts p WHERE p.forum_id = f.uid AND deleted=0 ORDER BY p.post_time DESC LIMIT 1),
					   forum_posts=(SELECT COUNT(1) FROM tx_mmforum_posts p WHERE p.forum_id = f.uid AND deleted=0)";
		$this->localDatabase->exec($forums);

		$topics = "UPDATE tx_mmforum_topics t SET
		               topic_replies = (SELECT COUNT(1)-1 FROM tx_mmforum_posts p WHERE p.topic_id = t.uid AND deleted=0),
					   topic_last_post_id = (SELECT uid FROM tx_mmforum_posts p WHERE p.topic_id = t.uid AND deleted=0 ORDER BY p.post_time DESC LIMIT 1),
					   topic_first_post_id = (SELECT uid FROM tx_mmforum_posts p WHERE p.topic_id = t.uid AND deleted=0 ORDER BY p.post_time ASC LIMIT 1)";
		$this->localDatabase->exec($topics);

		$users = "UPDATE fe_users u SET
		              tx_mmforum_posts = (SELECT COUNT(1) FROM tx_mmforum_posts p WHERE p.poster_id = u.uid AND deleted=0)";
		$this->localDatabase->exec($users);

	}





		/*
		 * HELPER METHODS
		 */





		/**
		 *
		 * Truncates database tables that are required to be empty before import.
		 * @return void
		 *
		 */

	Protected Function truncateTables() {
		$count = 0;

		$existingTables = Array();
		ForEach($this->localDatabase->query("SHOW TABLES", PDO::FETCH_COLUMN, 0) As $table)
			$existingTables[] = $table;

		ForEach($this->importConfiguration->getTruncateTables() As $truncateTable) {
			If(!in_array($truncateTable, $existingTables)) Throw New Exception ("Table $truncateTable does not exist!");
			$this->localDatabase->exec("TRUNCATE TABLE ".$truncateTable);
			$count ++;
		}
		$this->pushLog("Truncated $count tables.");
	}



		/**
		 *
		 * Clears some directories that are required to be empty before import.
		 * @return void
		 *
		 */

	Protected Function clearDirectories() {
		ForEach($this->importConfiguration->getClearDirectories() As $directory) {
			$directory = PATH_site.$directory;
			If(strpos($directory, '..') !== FALSE) Throw New Exception ("Directory $directory contains ..!");
			If(substr($directory, -1) != '/') $directory .= '/';

			$files = glob($directory.'*');
			ForEach($files As $file) $this->recursiveDelete($file);
		} $this->pushLog("Cleared ".count($this->importConfiguration->getClearDirectories())." directories.");
	}



		/**
		 *
		 * Recursively deletes a directory and all its contents.
		 * @return void
		 *
		 */

	Private Function recursiveDelete($dirname) {
		echo "rm $dirname<br />";
		$files = glob($dirname.'*', GLOB_MARK);
		ForEach($files As $file)
			If(is_dir($file)) $this->recursiveDelete($file);
			Else unlink($file);
		If(is_dir($dirname)) rmdir($dirname);
	}



		/**
		 *
		 * Pushes a log message onto the internal log stack.
		 *
		 * @param string  $message   The message
		 * @param array   $arguments Sprintf arguments for the message
		 * @param integer $logLevel  The severity of the log message.
		 * @return void
		 *
		 */

	Public Function pushLog($message, $arguments=Array(), $logLevel = 2) {
		$this->log[] = t3lib_div::makeInstance ( 'Tx_MmForumImport_Domain_Model_TestWarning',
		                                         $message, $arguments, $logLevel );
	}



		/**
		 *
		 * Returns all log messages for display.
		 * @return Array All log messages for display
		 *
		 */

	Public Function getLogMessages() { Return $this->log; }

}

?>
