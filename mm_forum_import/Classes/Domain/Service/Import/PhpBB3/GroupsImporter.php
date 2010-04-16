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
	 * Imports phpBB3 groups into the mm_forum database.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_GroupsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for inserting a new group
		 */
	Const STMT_INSERT_GROUP =
		"INSERT INTO fe_groups (pid, title, description, tx_mmforum_rank, tx_mmforum_rank_excl)
		 VALUES (:pid, :title, :description, :rank, :rank_excl)";

		/**
		 * Statement for selecting existing user groups.
		 */
	Const STMT_SELECT_GROUPS = "SELECT * FROM {PREFIX}groups";





		/*
		 * IMPORT PROCEDURE
		 */





		/**
		 *
		 * Imports groups.
		 * @return void
		 *
		 */

	Public Function importGroups() {

		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_GROUP  );
		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_GROUPS );

		$groupCount = 0;
		ForEach($this->remoteDatabase->query("SELECT * FROM {$this->prefix}groups") As $group) {
			$insertArray = Array ( ':pid'         => $this->importConfiguration->getUserPid(),
			                       ':title'       => $this->_d($group['group_name']),
			                       ':description' => $this->_d($group['group_desc']),
			                       ':rank'        => $group['group_rank'] ? $this->uidMapping['ranks'][$group['group_rank']] : '',
			                       ':rank_excl'   => $group['group_rank'] ? 1 : 0 );

			$insertStatement->execute($insertArray);
			$this->uidMapping['groups'][$group['group_id']] = $this->localDatabase->lastInsertId();
			$groupCount ++;
		}

		$this->pushLog("Imported $groupCount user groups.");

	}

}

?>