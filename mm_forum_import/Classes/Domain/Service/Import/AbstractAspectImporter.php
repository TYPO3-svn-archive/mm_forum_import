<?php

Abstract Class Tx_MmForumImport_Domain_Service_Import_AbstractAspectImporter {

		/**
		 * @var PDO
		 */
	Protected $localDatabase;

		/**
		 * @var PDO
		 */
	Protected $remoteDatabase;

		/**
		 * @var Array
		 */
	Protected $uidMapping;

		/**
		 * @var Tx_MmForumImport_Domain_Model_ImportConfiguration
		 */
	Protected $importConfiguration;

		/**
		 * @var Tx_MmForumImport_Domain_Service_Import_AbstractImporter
		 */
	Protected $parentImporter;

		/**
		 * @var Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface
		 */
	Protected $fileInterface = NULL;

		/*
		 * DEPENDENCY INJECTIONS
		 */

	Public Function injectLocalDatabaseConnection(PDO $localDatabase) {
		$this->localDatabase =& $localDatabase;
		Return $this;
	}

	Public Function injectRemoteDatabaseConnection(PDO $remoteDatabase) {
		$this->remoteDatabase =& $remoteDatabase;
		Return $this;
	}

	Public Function injectUidMapping(Array &$uidMapping) {
		$this->uidMapping =& $uidMapping;
		Return $this;
	}

	Public Function injectImportConfiguration(Tx_MmForumImport_Domain_Model_ImportConfiguration $importConfiguration) {
		$this->importConfiguration =& $importConfiguration;
		$this->prefix = $this->importConfiguration->getDatabaseSettings()->getPrefix();
		Return $this;
	}

	Public Function injectParentObject(Tx_MmForumImport_Domain_Service_Import_AbstractImporter $parent) {
		$this->parentImporter =& $parent;
		Return $this;
	}

	Public Function injectFileInterface(Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface $fileInterface) {
		$this->fileInterface =& $fileInterface;
		Return $this;
	}

	Protected Function pushLog($message, $arguments=Array(), $logLevel = Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_SUCCESS) {
		$this->parentImporter->pushLog($message, $arguments, $logLevel);
	}

	Protected Function getRemoteQuery($query) {
		Return str_replace('{PREFIX}', $this->prefix, $query);
	}

}

?>
