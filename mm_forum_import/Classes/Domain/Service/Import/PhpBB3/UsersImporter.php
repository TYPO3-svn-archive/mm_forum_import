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
	 * Imports users from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_UsersImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_USER =
		"INSERT INTO fe_users ( pid, username, password, tstamp, crdate, name, email, tx_mmforum_icq,
			                    tx_mmforum_aim, tx_mmforum_yim, tx_mmforum_msn, tx_mmforum_user_sig,
								tx_mmforum_interests, tx_mmforum_occ, usergroup)
		 VALUES ( :pid, :username, :password, UNIX_TIMESTAMP(), :crdate, :name, :email,
		          :icq, :aim, :yim, :msn, :signature, :interests, :occupation, :usergroup)";
	Const STMT_SELECT_USERS = "SELECT * FROM {PREFIX}users";
	Const STMT_SELECT_USER_GROUPS = "SELECT group_id FROM {PREFIX}user_group WHERE user_id=?";

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importUsers() {

		$insertStatement      = $this->localDatabase->prepare  ( self::STMT_INSERT_USER  );
		$selectStatement      = $this->getRemoteQuery          ( self::STMT_SELECT_USERS );
		$groupSelectStatement = $this->remoteDatabase->prepare ( $this->getRemoteQuery(self::STMT_SELECT_USER_GROUPS) );

		$userCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $user) {

			$groupSelectStatement->execute(Array($user['user_id']));
			$groupIds = $groupSelectStatement->fetchAll(PDO::FETCH_COLUMN, 0);

			ForEach($groupIds As &$groupId)
				$groupId = $this->uidMapping['groups'][$groupId];

			$insertArray = Array ( ':pid'        => $this->importConfiguration->getUserPid(),
			                       ':username'   => $user['username_clean'],
			                       ':name'       => $this->_d($user['username']),
			                       ':password'	 => md5(rand(0, 1000000).microtime()),
			                       ':email'	     => $user['user_email'],
			                       ':signature'	 => $this->_d($this->_processPostText($user['user_sig'])),
			                       ':icq'        => $user['user_icq'],
			                       ':aim'        => $user['user_aim'],
			                       ':yim'        => $user['user_yim'],
			                       ':msn'        => $user['user_msnm'],
			                       ':occupation' => $user['user_occ'],
			                       ':interests'  => $user['user_interests'],
			                       ':crdate'     => $user['user_regdate'],
			                       ':usergroup'  => implode(',', $groupIds) );
			$insertStatement->execute($insertArray);
			$this->uidMapping['users'][$user['user_id']] = $this->localDatabase->lastInsertId();
			$userCount ++;
		}
		$this->pushLog("Imported $userCount users.");

	}

}

?>