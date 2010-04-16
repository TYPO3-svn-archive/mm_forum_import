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
	 * Imports topics from a CHC Forum installation into the mm_forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_ChcForum
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_ChcForum_TopicsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for inserting a new topic
		 */
	Const STMT_INSERT_TOPIC =
		"INSERT INTO tx_mmforum_topics ( pid, tstamp, crdate, topic_title, topic_poster, topic_time, topic_views, forum_id, at_top_flag, closed_flag, poll_id )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :subject, :user, :crdate, :views, :forum, :sticky, :locked, :poll )";

		/**
		 * Statement for selecting existing topics
		 */
	Const STMT_SELECT_TOPICS = "SELECT * FROM tx_chcforum_thread WHERE deleted=0";





		/*
		 * IMPORT PROCEDURE
		 */





		/**
		 *
		 * Imports topics from a CHC Forum installation into the mm_forum.
		 * @return void
		 *
		 */
	
	Public Function importTopics() {

		$selectStatement               = self::STMT_SELECT_TOPICS;
		$insertTopicStatement          = $this->localDatabase->prepare ( self::STMT_INSERT_TOPIC       );

		$topicCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $topic) {

			$insertArray = Array (
				':pid'      => $this->importConfiguration->getForumPid(),
				':subject'  => $topic['thread_subject'],
				':user'     => $topic['thread_author'],
				':crdate'   => $topic['thread_datetime'],
				':views'    => $topic['thread_views'],
				':locked'   => $topic['thread_closed'] ? 1 : 0,
				':sticky'   => 0,
				':forum'    => $this->uidMapping['forums'][$topic['conference_id']],
				':poll'     => 0
			);
			$insertTopicStatement->execute($insertArray);
			$this->uidMapping['topics'][$topic['uid']] = $this->localDatabase->lastInsertId();
			$topicCount ++;

		}

		$this->pushLog("Imported $topicCount topics.");

	}

}

?>