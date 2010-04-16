<?php

########################################################################
# Extension Manager/Repository config file for ext "mm_forum_import".
#
# Auto generated 16-04-2010 09:54
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title'            => 'mm_forum import module',
	'description'      => 'Imports data from forum systems like phpBB into the mm_forum',
	'category'         => 'module',
	'author'           => 'Martin Helmich',
	'author_email'     => 'm.helmich@mittwald.de',
	'shy'              => '',
	'dependencies'     => 'mm_forum,extbase,fluid',
	'conflicts'        => '',
	'priority'         => '',
	'module'           => '',
	'state'            => 'alpha',
	'internal'         => '',
	'uploadfolder'     => 0,
	'createDirs'       => '',
	'modify_tables'    => '',
	'clearCacheOnLoad' => 0,
	'lockType'         => '',
	'author_company'   => 'Mittwald CM Service GmbH & Co. KG',
	'version'          => '1.0.0',
	'constraints'      => array (
		'depends'  => array ( 'mm_forum' => '1.9.0-0.0.0',
		                      'extbase'  => '1.0.0-0.0.0',
		                      'fluid'    => '1.0.0-0.0.0' ),
		'conflicts' => array(),
		'suggests'  => array(),
	),
	'_md5_values_when_last_written' => 'a:53:{s:12:"ext_icon.gif";s:4:"167d";s:17:"ext_localconf.php";s:4:"d3a0";s:37:"Classes/Controller/MainController.php";s:4:"41a6";s:44:"Classes/Domain/Model/ImportConfiguration.php";s:4:"7ff5";s:37:"Classes/Domain/Model/ImportSource.php";s:4:"8db7";s:36:"Classes/Domain/Model/TestWarning.php";s:4:"f151";s:53:"Classes/Domain/Model/ImportConfiguration/Database.php";s:4:"e9c1";s:58:"Classes/Domain/Model/ImportConfiguration/FileInterface.php";s:4:"0c8a";s:52:"Classes/Domain/Repository/ImportSourceRepository.php";s:4:"f0ca";s:52:"Classes/Domain/Service/ImportSourceReaderService.php";s:4:"be18";s:62:"Classes/Domain/Service/FileInterface/AbstractFileInterface.php";s:4:"7419";s:57:"Classes/Domain/Service/FileInterface/FTPFileInterface.php";s:4:"d024";s:62:"Classes/Domain/Service/FileInterface/FileNotFoundException.php";s:4:"bc1a";s:59:"Classes/Domain/Service/FileInterface/LocalFileInterface.php";s:4:"96f4";s:56:"Classes/Domain/Service/Import/AbstractAspectImporter.php";s:4:"ed1b";s:50:"Classes/Domain/Service/Import/AbstractImporter.php";s:4:"28cc";s:42:"Classes/Domain/Service/Import/ChcForum.php";s:4:"64ef";s:40:"Classes/Domain/Service/Import/PhpBB3.php";s:4:"a11a";s:57:"Classes/Domain/Service/Import/ChcForum/ForumsImporter.php";s:4:"5233";s:56:"Classes/Domain/Service/Import/ChcForum/PostsImporter.php";s:4:"2370";s:57:"Classes/Domain/Service/Import/ChcForum/TopicsImporter.php";s:4:"696a";s:63:"Classes/Domain/Service/Import/PhpBB3/AbstractAspectImporter.php";s:4:"6e18";s:55:"Classes/Domain/Service/Import/PhpBB3/ForumsImporter.php";s:4:"835d";s:55:"Classes/Domain/Service/Import/PhpBB3/GroupsImporter.php";s:4:"7c05";s:57:"Classes/Domain/Service/Import/PhpBB3/MessagesImporter.php";s:4:"f16e";s:54:"Classes/Domain/Service/Import/PhpBB3/PostsImporter.php";s:4:"5ca1";s:57:"Classes/Domain/Service/Import/PhpBB3/ProfilesImporter.php";s:4:"88ec";s:54:"Classes/Domain/Service/Import/PhpBB3/RanksImporter.php";s:4:"f7d5";s:56:"Classes/Domain/Service/Import/PhpBB3/ReportsImporter.php";s:4:"bfe6";s:56:"Classes/Domain/Service/Import/PhpBB3/SmiliesImporter.php";s:4:"63f6";s:55:"Classes/Domain/Service/Import/PhpBB3/TopicsImporter.php";s:4:"6907";s:54:"Classes/Domain/Service/Import/PhpBB3/UsersImporter.php";s:4:"f5da";s:48:"Classes/Domain/Service/Tester/AbstractTester.php";s:4:"fbc1";s:56:"Classes/Domain/Service/Tester/DatabaseSettingsTester.php";s:4:"dc2d";s:61:"Classes/Domain/Service/Tester/FileInterfaceSettingsTester.php";s:4:"9cc1";s:39:"Classes/ViewHelpers/ImageViewHelper.php";s:4:"cfcd";s:40:"Configuration/ImportSources/chcforum.xml";s:4:"55d9";s:38:"Configuration/ImportSources/phpbb3.xml";s:4:"da1f";s:40:"Configuration/Typoscript/PageTsConfig.ts";s:4:"e7b7";s:40:"Resources/Private/Language/locallang.xml";s:4:"4897";s:48:"Resources/Private/Templates/Main/datasource.html";s:4:"b632";s:43:"Resources/Private/Templates/Main/index.html";s:4:"195c";s:51:"Resources/Private/Templates/Main/performimport.html";s:4:"cbaa";s:57:"Resources/Private/Templates/Main/selectimportobjects.html";s:4:"0904";s:52:"Resources/Private/Templates/Main/testdatasource.html";s:4:"f218";s:37:"Resources/Public/Icons/datasource.png";s:4:"8095";s:37:"Resources/Public/Icons/filesource.png";s:4:"9cdd";s:36:"Resources/Public/Icons/warning-0.png";s:4:"ea9a";s:36:"Resources/Public/Icons/warning-1.png";s:4:"4bf6";s:36:"Resources/Public/Icons/warning-2.png";s:4:"89d9";s:36:"Resources/Public/Icons/warning-3.png";s:4:"c3a4";s:43:"Resources/Public/SoftwareIcons/chcforum.png";s:4:"1d8b";s:40:"Resources/Public/SoftwareIcons/phpbb.png";s:4:"40f8";}',
	'suggests' => array(),
);

?>
