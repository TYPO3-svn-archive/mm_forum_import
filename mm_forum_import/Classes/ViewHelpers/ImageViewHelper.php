<?php

Class Tx_MmForumImport_ViewHelpers_ImageViewHelper Extends Tx_Fluid_Core_ViewHelper_TagBasedViewHelper {

	protected $tagName = 'img';

	Public Function initializeArguments() {
		parent::initializeArguments();
		$this->registerTagAttribute('style', 'string', 'Inline CSS styles');
		$this->registerTagAttribute('width', 'int', 'Image width');
	}

		/**
		 * @param string $src
		 */
	Public Function render($src) {
		$src = $GLOBALS['BACK_PATH'].'../'.preg_replace_callback('/^EXT:([a-z0-9_-]+)\//', Array($this, '_replaceExtPaths'), $src);
		$this->tag->addAttribute('src', $src);
		Return $this->tag->render();
	}

	Public Function _replaceExtPaths($matches) {
		Return t3lib_extMgm::siteRelPath($matches[1]);
	}

}

?>