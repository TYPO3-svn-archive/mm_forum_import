<?php

if (TYPO3_MODE === 'BE') {
	Tx_Extbase_Utility_Extension::registerModule(
		'mm_forum_import',
		'mmforum',
		'm1',
		'',
		array( 'Main' => 'index,dataSource,testDataSource,selectImportObjects,performImport' ),
		array( 'Main' => 'index,dataSource,testDataSource,selectImportObjects,performImport' )
	);
}

?>