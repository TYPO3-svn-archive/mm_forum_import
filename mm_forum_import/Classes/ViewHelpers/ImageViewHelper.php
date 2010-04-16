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
	 * View Helper that displays an image in the TYPO3 backend. This class is necessary
	 * because the default Fluid image-ViewHelper does not yet work in a backend module.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage ViewHelpers
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_ViewHelpers_ImageViewHelper Extends Tx_Fluid_Core_ViewHelper_TagBasedViewHelper {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The tag name.
		 * @var string
		 */
	Protected $tagName = 'img';





		/*
		 * INITIALIZATION
		 */





		/**
		 *
		 * Initialize some basic tag attributes for this ViewHelper.
		 * @return void
		 *
		 */

	Public Function initializeArguments() {
		parent::initializeArguments();
		$this->registerTagAttribute('style', 'string', 'Inline CSS styles');
		$this->registerTagAttribute('width', 'int', 'Image width');
	}





		/*
		 * RENDER METHODS
		 */





		/**
		 *
		 * Render the view helper. The "src" attribute may contain a reference to an
		 * extension path (e.g. "EXT:mm_forum/res/...").
		 *
		 * @param  string $src The image source
		 * @return string      XHTML content
		 *
		 */

	Public Function render($src) {
		$src = $GLOBALS['BACK_PATH'].'../'.preg_replace_callback('/^EXT:([a-z0-9_-]+)\//', Array($this, '_replaceExtPaths'), $src);
		$this->tag->addAttribute('src', $src);
		Return $this->tag->render();
	}



		/**
		 *
		 * Replaces extension path references. For compatibility with PHP 5.2, we cannot
		 * use a lambda function for this. Pity.
		 *
		 * @param  array $matches RegExp matches
		 * @return string         The extension path.
		 *
		 */

	Public Function _replaceExtPaths($matches) {
		Return t3lib_extMgm::siteRelPath($matches[1]);
	}

}

?>