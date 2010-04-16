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
	 * Imports custom profile fields from the phpBB forum.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_Import_PhpBB3
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_Import_PhpBB3_ProfilesImporter
	Extends Tx_MmForumImport_Domain_Service_Import_PhpBB3_AbstractAspectImporter {

		/*
		 * CONSTANTS
		 */

	Const STMT_INSERT_FIELD =
		"INSERT INTO tx_mmforum_userfields ( pid, label, config, meta, public, uniquefield )
		 VALUES (:pid, :label, :config, :meta, :public, :unique)";
	Const STMT_SELECT_FIELD =
		"SELECT pf.*, lang_name
		 FROM        {PREFIX}profile_fields pf
		        JOIN {PREFIX}profile_lang pfl ON pfl.field_id = pf.field_id AND pfl.lang_id=1";
	Const STMT_SELECT_FIELD_OPTIONS =
		"SELECT lang_value, option_id FROM {PREFIX}profile_fields_lang WHERE lang_id=1 AND field_id=?";
	Const STMT_SELECT_FIELD_CONTENTS =
		"SELECT * FROM {PREFIX}profile_fields_data";
	Const STMT_INSERT_FIELD_CONTENTS =
		"INSERT INTO tx_mmforum_userfields_contents (pid, user_id, field_id, field_value)
		 VALUES ( :pid, :user, :field, :value )";

		/*
		 * ATTRIBUTES
		 */

	Protected $identifierMapping = Array();

		/*
		 * IMPORT PROCEDURE
		 */

	Public Function importProfiles() {
		$this->importProfileFields();
		$this->importProfileFieldContents();
	}

	Protected Function importProfileFields() {

		$insertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_FIELD );
		$selectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_FIELD );

		Require_Once ( t3lib_extMgm::extPath('mm_forum').'mod1/class.tx_mmforum_userfields.php' );

		$profileFieldCount = 0;
		ForEach($this->remoteDatabase->query($selectStatement) As $profileField) {

			$label = $profileField['lang_name'];
			$meta = Array ( 'label'    => Array ( 'default' => $label ),
			                'required' => $profileField['field_required'],
			                'private'  => $profileField['field_show_profile'] ? FALSE : TRUE );
			$config = NULL;

			If($profileField['field_type'] == 1) { # Number
				$meta['type'] = 'text';
				$meta['text'] = Array ( 'validate' => 'num',
				                        'length' => $profileField['field_maxlen'] );
			} ElseIf($profileField['field_type'] == 2) { # Text field
				$meta['type'] = 'text';
				$meta['text'] = Array ( 'length' => $profileField['field_maxlen'] );
			} ElseIf($profileField['field_type'] == 3) { # Text area
				list($rows, $cols) = explode('|', $profileField['field_length']);
				$meta['type'] = 'custom';
				$config = "label = TEXT\nlabel.value = $label\nvalidate = /".$profileField['field_validation']."/\n"
							."input = HTML\ninput.value = <textarea rows=\"$rows\" cols=\"$cols\" "
							."name=\"###USERFIELD_NAME###\">###USERFIELD_VALUE###</textarea>";
			} ElseIf($profileField['field_type'] == 4) { # Boolean
				$meta['type'] = 'radio';
				$options = Array();
				$optionQuery = $this->remoteDatabase->prepare($this->getRemoteQuery(self::STMT_SELECT_FIELD_OPTIONS));
				$optionQuery->execute(array($profileField['field_id']));
				While($optionRow = $optionQuery->fetch())
					$meta['radio']['value'][$optionRow['option_id']] = $optionRow['lang_value'];
			} ElseIf($profileField['field_type'] == 5) { # Dropdown
				$meta['type'] = 'select';
				$options = Array();
				$optionQuery = $this->remoteDatabase->prepare($this->getRemoteQuery(self::STMT_SELECT_FIELD_OPTIONS));
				$optionQuery->execute(array($profileField['field_id']));
				While($optionRow = $optionQuery->fetch())
					$meta['select']['value'][$optionRow['option_id']] = $optionRow['lang_value'];
			} Else Continue;

			If($config === NULL) {
				$userFields = New tx_mmforum_userFields();
				$configArr = $userFields->generateTSConfig($meta);
				$config = $this->importConfiguration->getParentObject()->parseConf($configArr);
			}

			$insertArray = Array ( ':pid'    => $this->importConfiguration->getForumPid(),
			                       ':label'  => $label,
			                       ':config' => $config,
			                       ':meta'   => serialize($meta),
			                       ':public' => $profileField['field_show_profile'] ? 1 : 0,
			                       ':unique' => 0 );
			$insertStatement->execute($insertArray);
			$this->identifierMapping['pf_'.$profileField['field_ident']] = $this->localDatabase->lastInsertId();
			$profileFieldCount ++;
		}

		$this->pushLog("Imported $profileFieldCount profile fields.");
	}

	Protected Function importProfileFieldContents() {
		$valueSelectStatement = $this->getRemoteQuery         ( self::STMT_SELECT_FIELD_CONTENTS );
		$valueInsertStatement = $this->localDatabase->prepare ( self::STMT_INSERT_FIELD_CONTENTS );

		ForEach($this->remoteDatabase->query($valueSelectStatement, PDO::FETCH_ASSOC) As $value) {
			ForEach($value As $fieldName => $fieldValue) {
				If($fieldName == 'user_id') Continue;
				$insertArray = Array ( ':pid'   => $this->importConfiguration->getForumPid(),
				                       ':user'  => $this->uidMapping['users'][$value['user_id']],
				                       ':field' => $this->identifierMapping[$fieldName],
				                       ':value' => $fieldValue );
				$valueInsertStatement->execute($insertArray);
			}
		}
	}

}

?>
