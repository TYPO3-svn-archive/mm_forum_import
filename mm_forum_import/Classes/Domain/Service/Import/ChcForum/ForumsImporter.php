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
	 * Imports categories and conferences from a CHC Forum installation into the mm_forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_ChcForum
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_ChcForum_ForumsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for inserting new forums.
		 */
	Const STMT_INSERT_FORUM =
		"INSERT INTO tx_mmforum_forums (pid, tstamp, crdate, forum_name, forum_desc, forum_order, sorting, parentID)
		 VALUES ( :pid, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :name, :description, :order, :order, :parent )";

		/**
		 * Statement for selecting categories from the CHC Forum tables
		 */
	Const STMT_SELECT_CATEGORIES = "SELECT * FROM tx_chcforum_category WHERE deleted=0 ORDER BY sorting";

		/**
		 * Statement for selecting conferences from the CHC FOrum tables
		 */
	Const STMT_SELECT_FORUMS = "SELECT * FROM tx_chcforum_conference WHERE deleted=0 AND cat_id=? ORDER BY sorting";





		/*
		 * IMPORT PROCEDURE
		 */





		/**
		 *
		 * Imports categories and conferences from the CHC Forum into the forums table
		 * of the mm_forum extension.
		 *
		 * @todo
		 *     At the moment, this method does not import the ACLs of the forums. This is
		 *     mainly done for consistency with the phpBB3 import.
		 * @return void
		 *
		 */

	Public Function importForums() {

		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_FORUM      );
		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_CATEGORIES );
		$forumSelectStatement = $this->localDatabase->prepare ( self::STMT_SELECT_FORUMS );

		$categoryCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $category) {
			$insertArray = Array ( ':pid'         => $this->importConfiguration->getForumPid(),
			                       ':name'        => $category['cat_title'],
			                       ':description' => $category['cat_description'],
			                       ':order'       => $categoryCount,
			                       ':parent'      => 0);

			$insertStatement->execute($insertArray);
			$this->uidMapping['categories'][$category['uid']] = $this->localDatabase->lastInsertId();
			$categoryCount ++;

			$i = 0;
			$forumSelectStatement->execute(Array($category['uid']));
			While($forum = $forumSelectStatement->fetch()) {
				$insertArray = Array ( ':pid' => $this->importConfiguration->getForumPid(),
				                       ':name' => $forum['conference_name'],
				                       ':description' => $forum['conference_desc'],
				                       ':order' => $i ++,
				                       ':parent' => $this->uidMapping['categories'][$category['uid']] );
				$insertStatement->execute($insertArray);
				$this->uidMapping['forums'][$forum['uid']] = $this->localDatabase->lastInsertId();
				$categoryCount ++;
			}
		}

		$this->pushLog("Imported $categoryCount categories and conferences.");

	}

}

?>
