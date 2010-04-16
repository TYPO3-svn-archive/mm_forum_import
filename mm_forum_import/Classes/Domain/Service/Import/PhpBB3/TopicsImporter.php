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
	 * Imports topics and polls from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_TopicsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_TOPIC =
		"INSERT INTO tx_mmforum_topics ( pid, tstamp, crdate, topic_title, topic_poster, topic_time, topic_views, forum_id, at_top_flag, closed_flag, poll_id )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :subject, :user, :crdate, :views, :forum, :sticky, :locked, :poll )";
	Const STMT_INSERT_POLL =
		"INSERT INTO tx_mmforum_polls ( pid, tstamp, crdate, endtime, question, votes )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :expire, :question, :votes )";
	Const STMT_INSERT_POLL_OPTION =
		"INSERT INTO tx_mmforum_polls_answers ( pid, tstamp, crdate, poll_id, votes, answer )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :poll, :votes, :answer )";
	Const STMT_INSERT_POLL_VOTE =
		"INSERT INTO tx_mmforum_polls_votes ( pid, tstamp, crdate, poll_id, answer_id, user_id )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :poll, :option, :user )";
	Const STMT_INSERT_TOPIC_SUBSCRIPTION =
		"INSERT INTO tx_mmforum_topicmail ( pid, tstamp, crdate, user_id, topic_id )
		 VALUES ( :pid, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :user, :topic )";

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importTopics() {
		$this->importRawTopics();
		$this->importTopicSubscriptions();
	}

	Protected Function importRawTopics() {

		$selectStatement               = "SELECT * FROM {$this->prefix}topics";
		$insertTopicStatement          = $this->localDatabase->prepare ( self::STMT_INSERT_TOPIC       );
		$pollInsertStatement           = $this->localDatabase->prepare ( self::STMT_INSERT_POLL        );
		$pollOptionInsertStatement     = $this->localDatabase->prepare ( self::STMT_INSERT_POLL_OPTION );
		$pollOptionVoteInsertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_POLL_VOTE   );

		$pollOptionSelectStatement     = $this->remoteDatabase->prepare ( "SELECT * FROM {$this->prefix}poll_options WHERE topic_id=?"     );
		$pollVoteSelectStatement       = $this->remoteDatabase->prepare ( "SELECT * FROM {$this->prefix}poll_votes WHERE topic_id=?"       );
		$pollOptionVoteSelectStatement = $this->remoteDatabase->prepare ( "SELECT * FROM {$this->prefix}poll_votes WHERE poll_option_id=?" );

		$topicCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $topic) {

			If($topic['topic_status'] == 2) Continue;
			If($topic['forum_id'] == 0)     Continue;

				# If there is a poll attached to the topic (if the "poll_start" attribute
				# of the topic is set), import this poll now.
			If($topic['poll_start'] > 0) {

				$pollOptionSelectStatement -> execute ( Array($topic['topic_id']) );
				$pollVoteSelectStatement   -> execute ( Array($topic['topic_id']) );

				$pollInsertArray = Array ( ':pid'      => $this->importConfiguration->getForumPid(),
				                           ':crdate'   => $topic['poll_start'],
				                           ':expire'   => $topic['poll_length'] ? ($topic['poll_start'] + $topic['poll_length']) : 0,
				                           ':question' => $this->_d($topic['poll_title']),
				                           ':votes'    => $pollVoteSelectStatement->rowCount() );
				$pollInsertStatement->execute($pollInsertArray);
				$pollId = $this->localDatabase->lastInsertId();

					# IMPORT POLL OPTIONS [begin]
				While($pollOption = $pollOptionSelectStatement->fetch()) {
					$pollOptionVoteSelectStatement->execute ( Array($pollOption['poll_option_id']) );
					$pollOptionInsertArray = Array ( ':pid'    => $this->importConfiguration->getForumPid(),
					                                 ':crdate' => $topic['poll_start'],
					                                 ':poll'   => $pollId,
					                                 ':answer' => $this->_d($pollOption['poll_option_text']),
					                                 ':votes'  => $pollOptionVoteSelectStatement->rowCount() );
					$pollOptionInsertStatement->execute($pollOptionInsertArray);
					$pollOptionId = $this->localDatabase->lastInsertId();

						# IMPORT POLL VOTES [begin]
					While($pollVote = $pollOptionVoteSelectStatement->fetch()) {
						$pollOptionVoteInsertArray = Array ( ':pid'    => $this->importConfiguration->getForumPid(),
						                                     ':crdate' => $topic['poll_start'],
						                                     ':poll'   => $pollId,
						                                     ':option' => $pollOptionId,
						                                     ':user'   => $this->uidMapping['users'][$pollVote['vote_user_id']] );
						$pollOptionVoteInsertStatement->execute($pollOptionVoteInsertArray);
					} # IMPORT POLL VOTES [end]
				} # IMPORT POLL OPTION [end]
			} # IMPORT POLL [end]

			$insertArray = Array (
				':pid'      => $this->importConfiguration->getForumPid(),
				':subject'  => $this->_d($topic['topic_title']),
				':user'     => $this->uidMapping['users'][$topic['topic_poster']],
				':crdate'   => $topic['topic_time'],
				':views'    => $topic['topic_views'],
				':locked'   => $topic['topic_status'] == 1 ? 1 : 0,
				':sticky'   => ($topic['topic_type'] == 1 || $topic['topic_type'] == 2) ? 1 : 0,
				':forum'    => $this->uidMapping['forums'][$topic['forum_id']],
				':poll'     => $pollId ? $pollId : 0
			);
			$insertTopicStatement->execute($insertArray);
			$this->uidMapping['topics'][$topic['topic_id']] = $this->localDatabase->lastInsertId();
			$topicCount ++;

		}

		$this->pushLog("Imported $topicCount topics.");

	}

	Protected Function importTopicSubscriptions() {

		$selectStatement = "SELECT * FROM {$this->prefix}topics_watch";
		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_TOPIC_SUBSCRIPTION );

		ForEach($this->remoteDatabase->query($selectStatement) As $topicSubscription) {
			$insertArray = Array ( ':pid'   => $this->importConfiguration->getForumPid(),
			                       ':user'  => $this->uidMapping['users'][$topicSubscription['user_id']],
			                       ':topic' => $this->uidMapping['topics'][$topicSubscription['topic_id']] );
			$insertStatement->execute($insertArray);
		}

	}

}

?>