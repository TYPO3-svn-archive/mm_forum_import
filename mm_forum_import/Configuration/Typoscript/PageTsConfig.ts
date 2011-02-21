mod.web_txmmforumM1 {

	sections {

			# Disable the built-in import module, just in case.
		50 = MMFORUM_SECTION_ITEM_DISABLED

		400 = MMFORUM_SECTION_ITEM
		400.id   = import
		400.name = LLL:EXT:mm_forum_import/Resources/Private/Language/locallang.xml:mod.menu.import
	}

}

	# The bootstrap class was added in Extbase 1.3 (TYPO3 4.5), so we need to
	# reference the old dispatcher in 4.3 and 4.4.

[compatVersion = 4.3] || [compatVersion = 4.4]

mod.web_txmmforumM1.sections.400.handler = tx_extbase_dispatcher->dispatch
mod.web_txmmforumM1.sections.400.handler.settings {
	extensionName = MmForumImport
	controller = Main
	action = index
	switchableControllerActions.1 {
		controller = Main
		actions = index,dataSource,testDataSource,selectImportObjects,performImport
	}
}

[compatVersion = 4.5]

mod.web_txmmforumM1.sections.400.handler = EXT:mm_forum_import/Classes/Bootstrap.php:tx_MmForumImport_Bootstrap->run
mod.web_txmmforumM1.sections.400.handler.settings >

[global]