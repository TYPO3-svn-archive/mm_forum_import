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
	 * Imports smilies from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_SmiliesImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_SMILIE =
		"INSERT INTO tx_mmforum_smilies (pid, tstamp, crdate, code, smile_url, emoticon, hidden)
		 VALUES ( :pid, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :code, :icon, :description, :hidden )";
	Const STMT_SELECT_SMILIES =
		"SELECT * FROM {PREFIX}smilies ORDER BY smiley_order";

	Const SMILIE_PATH_PHPBB = 'images/smilies/';
	Const SMILIE_PATH_TYPO3 = 'uploads/tx_mmforum/';

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importSmilies() {

		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_SMILIES );
		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_SMILIE  );

		$smilieCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement, PDO::FETCH_ASSOC) As $smilie) {

			Try {
				$this->fileInterface->retrieveFile ( self::SMILIE_PATH_PHPBB . $smilie['smiley_url'],
				                                     self::SMILIE_PATH_TYPO3 . "smilies/".$smilie['smiley_url'] );
			} Catch (Tx_MmForumImport_Domain_Service_FileInterface_FileNotFoundException $e) {
				Continue;
			}

			$insertArray = Array ( ':pid'         => $this->importConfiguration->getForumPid(),
			                       ':code'        => '{s:'.$smilie['code'].'}',
			                       ':icon'        => 'smilies/'.$smilie['smiley_url'],
			                       ':description' => $smilie['emotion'],
			                       ':hidden'      => $smilie['display_on_posting'] ? 0 : 1 );
			$insertStatement->execute($insertArray);
			$smilieCount ++;
		}

		$this->pushLog("Imported $smilieCount smilies.");

	}

}

?>
