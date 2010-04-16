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
	 * Models a warning message that occurs while testing the import configuration.
	 * Objects of this type are created by the tester classes that test the file system
	 * access or the database access.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Model
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Model_TestWarning Extends Tx_Extbase_DomainObject_AbstractValueObject {





		/*
		 * CONSTANTS
		 */





		 /**
		  * Fatal severity
		  */
	Const SEVERITY_FATAL   = 0;

		/**
		 * Warnings
		 */
	Const SEVERITY_WARNING = 1;

		/**
		 * Notices
		 */
	Const SEVERITY_NOTICE  = 2;

		/**
		 * Success message
		 */
	Const SEVERITY_SUCCESS = 3;





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The message
		 * @var string
		 */
	Private $message;

		/**
		 * The severity.
		 * @var integer
		 */
	Private $severity;

		/**
		 * Additional sprintf arguments.
		 * @var array
		 */
	Private $arguments;





		/*
		 * CONSTRUCTORS
		 */





		 /**
		  *
		  * Create a new message.
		  *
		  * @param string  $message   The message
		  * @param array   $arguments Sprintf arguments for the message
		  * @param integer $severity  The severity
		  *
		  */

	Public Function __construct ( $message,
	                              Array $arguments = Array(),
	                              $severity        = Tx_MmForumImport_Domain_Model_TestWarning::SEVERITY_SUCCESS ) {
		$this->message = $message;
		$this->severity = $severity;
		$this->arguments = $arguments;
	}





		/*
		 * GETTER METHODS
		 */





		/**
		 *
		 * Gets the message
		 * @return string The message
		 *
		 */

	Public Function getMessage() { Return $this->message; }



		/**
		 *
		 * Gets the arguments for the message.
		 * @return array Arguments for the message
		 *
		 */

	Public Function getArguments() { Return $this->arguments; }



		/**
		 *
		 * Gets the severity.
		 * @return integer The severity
		 *
		 */

	Public Function getSeverity() { Return $this->severity; }



		/**
		 *
		 * Gets a color representation of the severity.
		 * @return string A color representation of the severity
		 *
		 */

	Public Function getSeverityColor() {
		Switch($this->severity) {
			Case self::SEVERITY_FATAL: Return 'ffd0d0';
			Case self::SEVERITY_WARNING: Return 'ffffa0';
			Case self::SEVERITY_SUCCESS: Return 'd0ffd0';
			Case self::SEVERITY_NOTICE: Return 'ffffff';
		}
	}

}

?>