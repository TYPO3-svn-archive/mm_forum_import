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
	 * Imports private messages from the phpBB forum into the mm_forum. A difficulty here
	 * is that the mm_forum stores every message as a copy for the sender. This means
	 * that every imported message needs to be inserted twice. Furthermore, phpBB
	 * messages may have more than one receipients.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_MessagesImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for selecting existing message
		 */
	Const STMT_SELECT_MESSAGE =
		"SELECT m.*, u.username FROM {PREFIX}privmsgs m JOIN {PREFIX}users u ON m.author_id = u.user_id";

		/**
		 * Statement for inserting a new message
		 */
	Const STMT_INSERT_MESSAGE =
		"INSERT INTO tx_mmforum_pminbox ( pid, tstamp, crdate, sendtime, from_uid, from_name,
		                                  to_uid, to_name, subject, message, read_flg, mess_type, deleted )
		 VALUES ( :pid, :crdate, :crdate, :crdate, :sender_uid, :sender_name, :receipient_uid,
		          :receipient_name, :subject, :message, :read, :type, :deleted )";

		/**
		 * Statement for selecting message receipients.
		 */
	Const STMT_SELECT_RECEIPIENTS =
		"SELECT r.*, u.username FROM {PREFIX}privmsgs_to r JOIN {PREFIX}users u ON u.user_id = r.user_id WHERE msg_id=?";





		/*
		 * IMPORT PROCEDURE
		 */





		/**
		 *
		 * Imports private messages.
		 * @return void
		 *
		 */

	Public Function importMessages() {

		$selectStatement           = $this->getRemoteQuery          ( self::STMT_SELECT_MESSAGE );
		$insertStatement           = $this->localDatabase->prepare  ( self::STMT_INSERT_MESSAGE );
		$receipientSelectStatement = $this->remoteDatabase->prepare ( $this->getRemoteQuery( self::STMT_SELECT_RECEIPIENTS ) );

		$messageCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $message) {

			$insertArrayTemplate = Array ( ':pid'         => $this->importConfiguration->getForumPid(),
			                               ':crdate'      => $message['message_time'],
			                               ':sender_uid'  => $this->uidMapping['users'][$message['author_id']],
			                               ':sender_name' => $message['username'],
			                               ':subject'     => $this->_d($message['message_subject']),
			                               ':message'     => $this->_d($this->_processPostText($message['message_text'])) );
			$receipientSelectStatement->execute(Array($message['msg_id']));

			While($receipient = $receipientSelectStatement->fetch()) {
				$insertArrayR = $insertArrayTemplate;
				$insertArrayR[':receipient_uid'] = $receipient['user_id'];
				$insertArrayR[':receipient_name'] = $receipient['username'];
				$insertArrayR[':read'] = $receipient['pm_unread'] ? 0 : 1;
				$insertArrayR[':deleted'] = $receipient['pm_deleted'] ? 1 : 0;
				$insertArrayR[':type'] = 0;

				$insertArrayS = Array ( ':pid'             => $this->importConfiguration->getForumPid(),
				                        ':crdate'          => $insertArrayR[':crdate'],
				                        ':sender_uid'      => $insertArrayR[':receipient_uid'],
				                        ':sender_name'     => $insertArrayR[':receipient_name'],
				                        ':receipient_uid'  => $insertArrayR[':sender_uid'],
				                        ':receipient_name' => $insertArrayR[':sender_name'],
				                        ':subject'         => $insertArrayR[':subject'],
				                        ':message'         => $insertArrayR[':message'],
				                        ':read'            => 1,
				                        ':deleted'         => 0,
				                        ':type'            => 1 );

				$insertStatement->execute($insertArrayR);
				$insertStatement->execute($insertArrayS);
			}

			$messageCount ++;

		}

		$this->pushLog("Imported $messageCount private messages.");

	}

}

?>
