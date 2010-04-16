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
	 * Imports user ranks from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_RanksImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_RANK =
		"INSERT INTO tx_mmforum_ranks (pid, tstamp, crdate, title, color, minPosts, special, icon)
		 VALUES ( :pid, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :title, :color, :min_posts, :special, :icon)";
	Const STMT_SELECT_RANKS = "SELECT * FROM {PREFIX}ranks";

	Const RANK_IMAGE_PATH_PHPBB = 'images/ranks/';
	Const RANK_IMAGE_PATH_TYPO3 = 'uploads/tx_mmforum/';

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importRanks() {

		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_RANK  );
		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_RANKS );

		$rankCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $rank) {
			If($rank['rank_image'])
				$this->fileInterface->retrieveFile ( self::RANK_IMAGE_PATH_PHPBB.$rank['rank_image'],
				                                     self::RANK_IMAGE_PATH_TYPO3.$rank['rank_image'] );
			$insertArray = Array ( ':pid'       => $this->importConfiguration->getUserPid(),
			                       ':title'     => $this->_d($rank['rank_title']),
			                       ':color'     => '',
			                       ':min_posts' => $rank['rank_min'],
			                       ':special'   => $rank['rank_special'] ? 1 : 0,
			                       ':icon'      => $rank['rank_image'] );
			$insertStatement->execute($insertArray);
			$this->uidMapping['ranks'][$rank['rank_id']] = $this->localDatabase->lastInsertId();
			$rankCount ++;
		}

		$this->pushLog("Imported $rankCount user ranks.");

	}

}

?>
