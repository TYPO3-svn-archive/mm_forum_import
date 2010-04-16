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
	 * Imports posts from the phpBB3 forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_PostsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_SELECT_POST =
		"SELECT * FROM {PREFIX}posts";
	Const STMT_INSERT_POST =
		"INSERT INTO tx_mmforum_posts ( pid, tstamp, crdate, topic_id, forum_id, poster_id, post_time, poster_ip, attachment )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :topic, :forum, :user, :crdate, :ip, :attachment )";
	Const STMT_INSERT_POST_TEXT =
		"INSERT INTO tx_mmforum_posts_text ( pid, tstamp, crdate, post_id, post_text )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :post, :text )";

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importPosts() {

		$insertPostStatement     = $this->localDatabase->prepare ( self::STMT_INSERT_POST      );
		$insertPostTextStatement = $this->localDatabase->prepare ( self::STMT_INSERT_POST_TEXT );
		$selectPostsStatement    = $this->getRemoteQuery         ( self::STMT_SELECT_POST      );

		$postCount = 0;
		ForEach($this->remoteDatabase->query($selectPostsStatement) As $post) {

			$insertArray = Array ( ':pid'        => $this->importConfiguration->getForumPid(),
			                       ':topic'      => $this->uidMapping['topics'][$post['topic_id']],
			                       ':forum'      => $this->uidMapping['forums'][$post['forum_id']],
			                       ':user'       => $this->uidMapping['users'][$post['poster_id']],
			                       ':crdate'     => $post['post_time'],
			                       ':ip'         => dechex(ip2long($post['poster_ip'])),
			                       ':attachment' => '' );
			$insertPostStatement->execute($insertArray);
			$postUid = $this->localDatabase->lastInsertId();
			$this->uidMapping['posts'][$post['post_id']] = $postUid;

			$text = $this->_processPostText($post['post_text']);
			If($post['post_subject']) $text = '[b]'.$post['post_subject']."[/b]\n\n".$text;

			$insertArray = Array ( ':pid'    => $this->importConfiguration->getForumPid(),
			                       ':crdate' => $post['post_time'],
			                       ':post'   => $postUid,
			                       ':text'   => $this->_d($text) );
			$insertPostTextStatement->execute($insertArray);
			$postCount ++;
		}

		$this->pushLog("Imported $postCount posts.");

	}

}

?>
