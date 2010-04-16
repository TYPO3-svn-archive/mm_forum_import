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
	 * Abstract aspect importer base class. This class provides methods needed by all
	 * phpBB3 aspect importers.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Abstract Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter
	Extends Tx_MmForumImport_Domain_Service_Import_AbstractAspectImporter {



		/**
		 *
		 * Processes a post text for being displayed in the mm_forum. This includes the
		 * following aspects:
		 * - Remove the identifier codes from bb code tags. So, for example:
		 *   "[b:123asd]bold[/b]" --> "[b]bold[/b]"
		 * - Remove HTML tags for smilie insertion:
		 *   "<!-- s;) --><img src="{SMILIE_PATH}/blink.gif" /><!-- s;) -->" --> "{s:;)}"
		 *
		 * @param  string $text The post text
		 * @return string       The better post text
		 *
		 */

	Protected Function _processPostText($text) {
		$text = preg_replace('/\[(.*?):([a-z0-9]+)\]/', '[\\1]', $text);
		$text = preg_replace('/<!-- s([^ ]+) --><img src="(:?.*)" title="(:?.*)" \/><!-- s\1 -->/', '{s:\1}', $text);
		Return $text;
	}



		/**
		 *
		 * Performs a charset conversion on a string and decodes HTML entities (why does
		 * phpBB have to store all texts encoded in the database!?)
		 *
		 * @param  string $text The string
		 * @return string       Better string
		 *
		 */

	Protected Function _d($text) {
		$text = iconv($this->importConfiguration->getSourceCharset(), 'utf-8', $text);
		$text = html_entity_decode($text);
		Return $text;
	}

}

?>
