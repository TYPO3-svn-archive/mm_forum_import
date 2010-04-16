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
	 * Imports posts and attachments from the CHC FOrum tables into the appropriate
	 * mm_forum tables.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_ChcForum
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_ChcForum_PostsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_AbstractAspectImporter {





		/*
		 * CONSTANTS
		 */





		/**
		 * Statement for selecting CHC Forum posts
		 */
	Const STMT_SELECT_POST = "SELECT * FROM tx_chcforum_post";

		/**
		 * Statement for inserting new posts
		 */
	Const STMT_INSERT_POST =
		"INSERT INTO tx_mmforum_posts ( pid, tstamp, crdate, topic_id, forum_id, poster_id, post_time, poster_ip, attachment )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :topic, :forum, :user, :crdate, :ip, :attachment )";

		/**
		 * Statement for inserting post texts
		 */
	Const STMT_INSERT_POST_TEXT =
		"INSERT INTO tx_mmforum_posts_text ( pid, tstamp, crdate, post_id, post_text )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :post, :text )";

		/**
		 * Statement for inserting post attachments
		 */
	Const STMT_INSERT_ATTACHMENT =
		"INSERT INTO tx_mmforum_attachments ( pid, tstamp, crdate, file_type, file_name, file_path, file_size, downloads, post_id )
		 VALUES ( :pid, UNIX_TIMESTAMP(), :crdate, :type, :name, :path, :size, :downloads, 0 )";

		/**
		 * Statement for updating attachments after the parent post has been saved.
		 */
	Const STMT_UPDATE_ATTACHMENT = "UPDATE tx_mmforum_attachments SET post_id=? WHERE uid=?";

		/**
		 * Path where CHC Forum attachments are being stored.
		 */
	Const PATH_ATTACHMENT_CHCFORUM = 'uploads/tx_chcforum/';

		/**
		 * Path where mm_forum attachments are being stored.
		 */
	Const PATH_ATTACHMENT_MMFORUM = 'uploads/tx_mmforum/';





		/*
		 * IMPORT PROCEDURES
		 */





		/**
		 *
		 * Imports all posts and attachments from the CHC forum tables into the mm_forum
		 * database.
		 *
		 * @return void
		 *
		 */

	Public Function importPosts() {

		$insertPostStatement     = $this->localDatabase->prepare ( self::STMT_INSERT_POST       );
		$insertPostTextStatement = $this->localDatabase->prepare ( self::STMT_INSERT_POST_TEXT  );
		$insertAttachStatement   = $this->localDatabase->prepare ( self::STMT_INSERT_ATTACHMENT );
		$updateAttachStatement   = $this->localDatabase->prepare ( self::STMT_UPDATE_ATTACHMENT );
		$selectPostsStatement    = $this->getRemoteQuery         ( self::STMT_SELECT_POST       );

		$postCount = 0;
		ForEach($this->remoteDatabase->query($selectPostsStatement) As $post) {

			If($post['post_attached']) {
				$localFilename = self::PATH_ATTACHMENT_MMFORUM.$post['post_attached'];
				$this->fileInterface->retrieveFile (
					self::PATH_ATTACHMENT_CHCFORUM.$post['post_attached'],
					$localFilename );
				$insertArray = Array ( ':pid'        => $this->importConfiguration->getForumPid(),
				                       ':crdate'     => $post['crdate'],
				                       ':type'       => $this->getMimeType(PATH_site.$localFilename),
				                       ':size'       => filesize($localFilename),
				                       ':path'       => $localFilename,
				                       ':downloads'  => 0 );
				$insertAttachStatement->execute($insertArray);
				$attachmentId = $this->localDatabase->lastInsertId();
			} Else $attachmentId = '';

			$insertArray = Array ( ':pid'        => $this->importConfiguration->getForumPid(),
			                       ':topic'      => $this->uidMapping['topics'][$post['thread_id']],
			                       ':forum'      => $this->uidMapping['forums'][$post['conference_id']],
			                       ':user'       => $this->uidMapping['users'][$post['post_author']],
			                       ':crdate'     => $post['crdate'],
			                       ':ip'         => dechex(ip2long($post['post_author_ip'])),
			                       ':attachment' => $attachmentId );
			$insertPostStatement->execute($insertArray);
			$postUid = $this->localDatabase->lastInsertId();
			$this->uidMapping['posts'][$post['post_id']] = $postUid;

			If($attachmentId != '')
				$updateAttachStatement->execute(Array($postUid, $attachmentId));

			$text = $post['post_text'];
			If($post['post_subject']) $text = '[b]'.$post['post_subject']."[/b]\n\n".$text;

			$insertArray = Array ( ':pid'    => $this->importConfiguration->getForumPid(),
			                       ':crdate' => $post['crdate'],
			                       ':post'   => $postUid,
			                       ':text'   => $text );
			$insertPostTextStatement->execute($insertArray);
			$postCount ++;
		}

		$this->pushLog("Imported $postCount posts.");

	}



		/**
		 *
		 * Tries to get the MIME type of a file. This is necessary, because every
		 * mm_forum post attachment needs a MIME type to be attached.
		 *
		 * @param  string $filename The filename, e.g. "uploads/tx_mmforum/hello.pdf"
		 * @return string           The MIME type, e.g. "application/x-pdf"
		 *
		 */

	Protected Function getMimeType($filename) {
		If (function_exists('mime_content_type'))
			Return mime_content_type($filename);
		ElseIf (function_exists('finfo_file')){
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $file_path);
			finfo_close($finfo);
			Return $mtype;
		} Else Return "application/force-download";
	}

}

?>
