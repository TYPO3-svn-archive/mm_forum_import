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
	 * Service class that reads XML files and maps the properties stored in these files
	 * to instances of the Tx_MmForumImport_Domain_Model_ImportSource class.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Service
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_ImportSourceReaderService {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * An instance of the DOC Document
		 * @var DOMDocument
		 */
	Private $dom = NULL;

		/**
		 * An XPath instance that is used to access the DOM Document
		 * @var DOMXPath
		 */
	Private $xpath = NULL;





		/*
		 * CONSTRUCTOR
		 */





		/**
		 *
		 * Creates an instance of this service class.
		 *
		 */

	Public Function __construct() {
		$this->dom   = New DOMDocument();
		$this->xpath = New DOMXPath($this->dom);
	}





		/*
		 * SERVICE METHODS
		 */





		/**
		 *
		 * Reads the properties of an Tx_MmForumImport_Domain_Model_ImportSource instance
		 * from an XML file.
		 *
		 * @param String $xmlFilename
		 *     The filename of the XML file that contains the import source properties.
		 * @param Tx_MmForumImport_Domain_Model_ImportSource &$importSource
		 *     The import source object. It is passed by referenc, so no return value
		 *     is necessary.
		 * @return Void
		 *
		 */

	Public Function readFromXMLFile($xmlFilename, Tx_MmForumImport_Domain_Model_ImportSource &$importSource) {
		$this->dom->load($xmlFilename);
		$this->xpath = New DOMXPath($this->dom);

		$propertyMap = Array (
			'identifier'            => basename($xmlFilename, '.xml'),
			'softwareName'          => $this->getNodeContent('//mmforum_import/software/name'),
			'softwareIcon'          => $this->getNodeContent('//mmforum_import/software/icon'),
			'dataSourceMode'        => $this->getNodeContent('//mmforum_import/software/importSource'),
			'hasDatabasePrefix'     => $this->getNodeContent('//mmforum_import/databaseRequirement/options/option[@name="queryPrefix"]') == 'yes',
			'queryForCharset'       => $this->getNodeContent('//mmforum_import/databaseRequirement/options/option[@name="queryCharset"]') == 'yes',
			'requiredTableNames'    => $this->getNodeContentArray('//mmforum_import/databaseRequirement/requiredTables/table'),
			'truncateTables'        => $this->getNodeContentArray('//mmforum_import/databaseRequirement/truncateTables/table'),
			'importItems'           => $this->getNodeContentArray('//mmforum_import/import/importItems/importItem'),
			'importClassName'       => $this->getNodeContent('//mmforum_import/import/importerClass'),
			'requireFileImport'     => $this->getNodeContent('//mmforum_import/fileImport/options/option[@name="enable"]') == 'yes',
			'allowedFileInterfaces' => $this->getNodeContentArray('//mmforum_import/fileImport/allowedInterfaces/interface'),
			'clearDirectories'      => $this->getNodeContentArray('//mmforum_import/fileImport/clearDirectories/directory'),
		);
		ForEach($propertyMap As $propertyName => &$propertyValue)
			$importSource->_setProperty($propertyName, $propertyValue);

	}





		/*
		 * HELPER METHODS
		 */





		/**
		 *
		 * Gets the content of a specific DOM node, or NULL if the node does not exist.
		 *
		 * @param  string $path A XPath description of the DOM node to be queried
		 * @return string       The content of the DOM node, or NULL if it does not exist.
		 *
		 */

	Private Function getNodeContent($path) {
		$nodeList = $this->xpath->query($path);
		Return $nodeList->length > 0 ? $nodeList->item(0)->nodeValue : NULL;
	}



		/**
		 *
		 * Reads the content of a specific DOM note into an array.
		 *
		 * @param  string $pathA XPath description of the DOM node to be queried
		 * @return array        The content of the DOM node, or NULL if it does not exist.
		 * 
		 */

	Private Function getNodeContentArray($path) {
		$resultList = Array();
		$nodeList = $this->xpath->query($path);
		ForEach($nodeList As $node) $resultList[] = $node->nodeValue;
		Return $resultList;
	}

}

?>