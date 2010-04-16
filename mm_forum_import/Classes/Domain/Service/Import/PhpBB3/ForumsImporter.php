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
	 * Imports phpBB3 forums into the mm_forum database. The only challenge is that in
	 * the phpBB3 forum, all forums may be nested indefinitely, while the mm_forum does
	 * not -- yet -- support this. In order to compensate this, all deeply nested phpBB
	 * forums will be mapped onto the same level.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_ForumsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for inserting a new forum
		 */
	Const STMT_INSERT_FORUM =
		"INSERT INTO tx_mmforum_forums (pid, tstamp, crdate, forum_name, forum_desc, forum_order, sorting, parentID)
		 VALUES ( :pid, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :name, :description, :order, :order, :parent )";

		/**
		 * Statement for selecting existing forums
		 */
	Const STMT_SELECT_FORUMS =
		"SELECT * FROM {PREFIX}forums WHERE parent_id=?";





		/*
		 * ATTRIBUTES
		 */





		/**
		 * Prepared statement for inserting new forums.
		 * @var PDOStatement
		 */
	Protected $forumInsertStatement = NULL;

		/**
		 * Counter for imported forums
		 * @var integer
		 */
	Protected $forumCount = 0;





		/*
		 * IMPORT PROCEDURE
		 */





		/**
		 *
		 * Imports all phpBB forums into the mm_forum database. This method is only a
		 * wrapper for the recursive "importChildForums" method.
		 *
		 * @return void
		 *
		 */

	Public Function importForums() {

		$this->forumInsertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_FORUM );
		$this->importChildForums(0);

		$this->pushLog("Imported {$this->forumCount} forums.");

	}



		/**
		 *
		 * Imports all child forums of a specific forum.
		 *
		 * @param integer $parentId
		 *     The UID of the parent forum
		 * @param integer $depth
		 *     Current recursion depth. This is needed to prevent deep nesting of
		 *     mm_forum forum objects.
		 * @param string  $parentTitles
		 *     The concatenated titles of all parent boards. From the second nesting
		 *     level on, these will be prepended to the imported forum title, in order to
		 *     visualize that these forums logically belong to their parent board.
		 * @param string  $newParentId
		 *     The new mm_forum parent board uid.
		 * @param integer &$i
		 *     Running variable. Needed to set the ordering value.
		 * @return void
		 *
		 */

	Protected Function importChildForums($parentId, $depth=0, $parentTitles = '', $newParentId = NULL, &$i=0) {

		$forumSelectStatement = $this->remoteDatabase->prepare($this->getRemoteQuery(self::STMT_SELECT_FORUMS));
		$forumSelectStatement->execute(Array($parentId));

		While($forum = $forumSelectStatement->fetch()) {

			$insertArray = Array ( ':pid'         => $this->importConfiguration->getForumPid(),
			                       ':name'        => $parentTitles.$this->_d($forum['forum_name']),
			                       ':description' => $this->_d($forum['forum_desc']),
			                       ':order'       => $i ++,
			                       ':parent'      => $parentId ? $this->uidMapping['forums'][($newParentId !== NULL) ? $newParentId : $parentId] : 0 );
			$this->forumInsertStatement->execute($insertArray);
			$this->uidMapping['forums'][$forum['forum_id']] = $this->localDatabase->lastInsertId();
			$this->forumCount ++;

			If($depth < 1)
				$this->importChildForums($forum['forum_id'], $depth + 1);
			Else $this->importChildForums($forum['forum_id'], $depth + 1,
				$parentTitles . $this->_d($forum['forum_name']).' :: ', $newParentId ? $newParentId : $parentId, $i);

		}

	}

}

?>
