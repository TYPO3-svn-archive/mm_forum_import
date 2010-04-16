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
	 * Domain object for the various import sources.
	 * This object break all Extbase conventions, since it is not being stored in a
	 * database table, but rather in an XML file. It is not filled by the default
	 * Extbase DataMapper, but by a specific service class.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Model
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Model_ImportSource Extends Tx_Extbase_DomainObject_AbstractDomainObject {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * Unique identifier for this software
		 * @var string
		 */
	Protected $identifier         = NULL;

		/**
		 * The name of this software
		 * @var string
		 */
	Protected $softwareName       = NULL;

		/**
		 * The filename of an icon for this software
		 * @var string
		 */
	Protected $softwareIcon       = NULL;

		/**
		 * Determines from where the import data is to be loaded. Default is 'database'.
		 * @var string
		 */
	Protected $dataSourceMode     = 'database';

		/**
		 * Determines, if the tables of this software are named with a prefix.
		 * @var boolean
		 */
	Protected $hasDatabasePrefix  = FALSE;

		/**
		 * Determines, whether the import source database may have a different charset.
		 * @var boolean
		 */
	Protected $queryForCharset    = FALSE;

		/**
		 * A list of database tables that are required for the import.
		 * @var array
		 */
	Protected $requiredTableNames = Array();

		/**
		 * A list of TYPO3 tables that are required to be truncated before import.
		 * @var array
		 */
	Protected $truncateTables     = Array();

		/**
		 * The name of the import class that will be used for importing.
		 * @var string
		 */
	Protected $importClassName    = NULL;

		/**
		 * A list of items that will be imported.
		 * @var array
		 */
	Protected $importItems        = Array();

		/**
		 * Determines whether the import module needs file system access to the import
		 * software installation.
		 * @var boolean
		 */
	Protected $requireFileImport     = TRUE;

		/**
		 * A list of allowed file interfaces for file system access.
		 * @var array
		 */
	Protected $allowedFileInterfaces = Array();

		/**
		 * A list of local directories that will be cleared before import.
		 * @var array
		 */
	Protected $clearDirectories      = Array();





		/*
		 * GETTER METHODS
		 */





		/**
		 *
		 * Gets the software name.
		 * @return string The software name
		 *
		 */

	Public Function getSoftwareName() { Return $this->softwareName; }



		/**
		 *
		 * Gets the software icon.
		 * @return string The software icon
		 *
		 */

	Public Function getSoftwareIcon() { Return $this->softwareIcon; }



		/**
		 *
		 * Gets the software identifier.
		 * @return string The software identifier.
		 *
		 */

	Public Function getIdentifier() { Return $this->identifier; }



		/**
		 *
		 * Gets the data source mode. Usually, this will always be the value 'database',
		 * since currently, there are no other possible values.
		 * @return string The data source mode.
		 *
		 */

	Public Function getDataSourceMode() { Return $this->dataSourceMode; }



		/**
		 *
		 * Determines if this software uses a database table name prefix.
		 * @return boolean TRUE, if this software uses a database table name prefix,
		 *                 otherwise FALSE.
		 *
		 */

	Public Function getHasDatabasePrefix() { Return $this->hasDatabasePrefix; }



		/**
		 *
		 * Determines whether the import module shall query for the charset that is used
		 * in the import source database.
		 * @return boolean TRUE, if a charset needs to be queried, otherwise FALSE.
		 *
		 */

	Public Function getQueryForCharset() { Return $this->queryForCharset; }



		/**
		 *
		 * Gets a list of database table that are required for the import.
		 * @return Array A list of database table that are required for the import
		 *
		 */

	Public Function getRequiredTableNames() { Return $this->requiredTableNames; }



		/**
		 *
		 * Gets a list of local TYPO3 tables that are required to be truncated before
		 * import.
		 * @return array A list of local TYPO3 tables that are required to be truncated
		 *               before import.
		 *
		 */

	Public Function getTablesToBeTruncated() { Return $this->truncateTables; }



		/**
		 *
		 * Determines if there are local tables that need to be truncated before import.
		 * @return boolean TRUE, if there are local tables that need to be truncated
		 *                 before import, otherwise FALSE.
		 *
		 */

	Public Function getHasTablesToBeTruncated() { Return is_array($this->truncateTables) && count($this->truncateTables) > 0; }



		/**
		 *
		 * Creates an instance of the importer class for this software.
		 *
		 * @return Tx_MmForumImport_Domain_Service_Import_AbstractImporter
		 *     An instance of the importer class for this software.
		 *
		 */

	Public Function getImporterInstance() {
		Return t3lib_div::makeInstance($this->importClassName);
	}



		/**
		 *
		 * Gets a list of all items that will be imported. For internal use only!
		 * @return Array A list of all items that will be imported
		 *
		 */

	Public Function getImportItems() { Return $this->importItems; }



		/**
		 *
		 * Determines if this software needs file system access to be imported.
		 * @return boolean TRUE, if this software needs file system access to be imported,
		 *                 otherwise FALSE.
		 *
		 */

	Public Function getDoesRequireFileImport() { Return $this->requireFileImport; }



		/**
		 *
		 * Gets a list of all allowed file system interfaces for file access.
		 * @return array A list of all allowed file system interfaces
		 *
		 */

	Public Function getAllowedFileInterfaces() { Return $this->allowedFileInterfaces; }



		/**
		 *
		 * Gets a list of all directories that are to be cleared before import.
		 * @return array A list of all directories that are to be cleared before import
		 *
		 */

	Public Function getDirectoriesToBeCleared() { Return $this->clearDirectories; }

}

?>