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
	 * Imports post reports from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_ReportsImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_REPORT =
		"INSERT INTO tx_mmforum_post_alert ( pid, tstamp, crdate, alert_text, post_id, topic_id, mod_id, status)
		 VALUES ( :pid, :crdate, :crdate, :reason, :post, :topic, :moderator, :status )";
	Const STMT_SELECT_REPORTS =
		"SELECT r.*, p.topic_id FROM {PREFIX}reports r JOIN {PREFIX}posts p ON p.post_id = r.post_id";

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importReports() {

		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_REPORTS );
		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_REPORT  );

		$reportCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement, PDO::FETCH_ASSOC) As $report) {

			$insertArray = Array ( ':pid'       => $this->importConfiguration->getForumPid(),
			                       ':crdate'    => $report['report_time'],
			                       ':reason'    => $this->_d($report['report_text']),
			                       ':post'      => $report['post_id'],
			                       ':topic'     => $report['topic_id'],
			                       ':moderator' => 0,
			                       ':status'    => $report['report_closed'] ? 1 : -1 );
			$insertStatement->execute($insertArray);
			$reportCount ++;

		}

		$this->pushLog("Imported $reportCount reports.");

	}

}

?>
