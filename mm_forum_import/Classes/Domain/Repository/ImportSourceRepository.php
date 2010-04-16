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
	 * The repository for import source software. This domain object breaks with Extbase
	 * conventions by NOT loading the software from the database, but rather from XML
	 * files stored in the extension repository.
	 * The contents from the XML files are then mapped to instances of the
	 * Tx_MmForumImport_Domain_Model_ImportSource class. This is done by a special
	 * service class, Tx_MmForumImport_Domain_Service_ImportSourceReaderService.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Repository
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Repository_ImportSourceRepository
	Implements Tx_Extbase_Persistence_RepositoryInterface {





		/*
		 * CONSTANTS
		 */





		/**
		 * Class name of the respective domain object.
		 */
	Const CLASS_IMPORTSOURCE_MODEL = 'Tx_MmForumImport_Domain_Model_ImportSource';

		/**
		 * Class name of the property mapping service.
		 */
	Const CLASS_IMPORTSOURCE_READERSERVICE = 'Tx_MmForumImport_Domain_Service_ImportSourceReaderService';





		/*
		 * ATTRIBUTES
		 */





		/**
		 * An instance of the import source reader service.
		 * @var Tx_MmForumImport_Domain_Service_ImportSourceReaderService
		 */
	Private $importSourceReaderService = NULL;





		/*
		 * CONSTRUCTOR
		 */





		/**
		 *
		 * Creates a new instance of this repository class.
		 *
		 */

	Public Function __construct() {
		$this->importSourceReaderService =& t3lib_div::makeInstance(self::CLASS_IMPORTSOURCE_READERSERVICE);
	}





		/*
		 * WRITE METHODS -- DUMMIES ONLY
		 */





		/**
		 *
		 * Adds an object. This is only a dummy method, because the import softwares are
		 * read-only.
		 * @param object $object Nah.
		 *
		 */

	Public Function add($object) { Throw New Exception("This operation is not supported!"); }




		/**
		 *
		 * REmoves an object. This is only a dummy method, because the import softwares
		 * are read-only.
		 * @param object $object Nah.
		 *
		 */

	Public Function remove($object) { Throw New Exception("This operation is not supported!"); }



		/**
		 *
		 * Gets all added software. This is only a dummy method, because the import
		 * softwares are read-only.
		 * @return array Empty array
		 *
		 */

	Public Function getAddedObjects() { Return Array(); }



		/**
		 *
		 * Gets all removed software. This is only a dummy method, because the import
		 * softwares are read-only.
		 * @return array Empty array
		 *
		 */

	Public Function getRemovedObjects() { Return Array(); }





		/*
		 * READ METHODS
		 */





		/**
		 *
		 * Finds all available import source software. Currently, this method only looks
		 * in the Configuration/ImportSources/ directory.
		 *
		 * @return Array<Tx_MmForumImport_Domain_Model_ImportSource>
		 *     An array containing all possible import source softwares.
		 *
		 */
	
	Public Function findAll() {
		$xmlFilenames = glob(t3lib_extMgm::extPath('mm_forum_import').'Configuration/ImportSources/*.xml');
		$importSources = Array();

		ForEach($xmlFilenames As &$xmlFilename) {
			$this->importSourceReaderService->readFromXMLFile
				($xmlFilename, $importSource = t3lib_div::makeInstance(self::CLASS_IMPORTSOURCE_MODEL));
			array_push($importSources, $importSource);
		} Return $importSources;
	}



		/**
		 *
		 * Finds a specific import source software.
		 *
		 * @param  string $uid The "uid" of the import source software. This is an
		 *                     identifier like e.g. "phpbb3".
		 * @return Tx_MmForumImport_Domain_Model_ImportSource
		 *     The import source software with the specified identifier.
		 *
		 */
	
	Public Function findByUid($uid) {
		If(!file_exists($xmlFilename = t3lib_extMgm::extPath('mm_forum_import').'Configuration/ImportSources/'.$uid.'.xml')) Return NULL;

		$importSource = t3lib_div::makeInstance(self::CLASS_IMPORTSOURCE_MODEL);
		$this->importSourceReaderService->readFromXMLFile($xmlFilename, $importSource);

		Return $importSource;
	}

}

?>