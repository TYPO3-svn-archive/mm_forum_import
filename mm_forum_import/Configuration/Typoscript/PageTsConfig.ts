mod.web_txmmforumM1 {

	sections {

			# Disable the built-in import module, just in case.
		50 = MMFORUM_SECTION_ITEM_DISABLED

		400 = MMFORUM_SECTION_ITEM
		400.id   = import
		400.name = LLL:EXT:mm_forum_import/Resources/Private/Language/locallang.xml:mod.menu.import
		#400.handler = EXT:mm_forum_import/Classes/Controller/MainController.php:Tx_MmForumImport_Controller_MainController->indexAction
		400.handler = tx_extbase_dispatcher->dispatch
		400.handler.settings {
			extensionName = MmForumImport
			controller = Main
			action = index
			switchableControllerActions.1 {
				controller = Main
				actions = index,dataSource,testDataSource,selectImportObjects,performImport
			}
			settings {
				foo = bar
			}
		}
	}

}