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
	 * Contains configuration data for the database connection that shall be used for the
	 * import.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Domain_Model_ImportConfiguration
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Model_ImportConfiguration_Database
	Extends Tx_Extbase_DomainObject_AbstractValueObject {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The database hostname.
		 * @var string
		 */
	Private $hostname;

		/**
		 * The database username
		 * @var string
		 */
	Private $username;

		/**
		 * The password
		 * @var string
		 */
	Private $password;

		/**
		 * The database name
		 * @var string
		 */
	Private $dbname;

		/**
		 * The table name prefix.
		 * @var string
		 */
	Private $prefix;

		/**
		 * The driver name
		 * @var string
		 */
	Private $driver = 'mysql';





		/*
		 * CONSTRUCTORS
		 */





		 /**
		  *
		  * Creates an instance of this class using data passed in a single array.
		  *
		  * @param  array $database The database connection data as array.
		  * @return Tx_MmForumImport_Domain_Model_ImportConfiguration_Database
		  *     An instance of this class containing the database connection data.
		  *
		  */

	Static Public Function createFromArray($database) {
		If(!$database['hostname'] || !$database['username'] || !$database['password'] || !$database['dbname'])
			Return NULL;
		Return New Tx_MmForumImport_Domain_Model_ImportConfiguration_Database (
				$database['hostname'], $database['username'], $database['password'],
				$database['dbname'], $database['prefix'], $database['driver'] );
	}



		/**
		 *
		 * Creates a new database connection configuration.
		 *
		 * @param string $hostname The hostname
		 * @param string $username The username
		 * @param string $password The password
		 * @param string $dbname   The database name
		 * @param string $prefix   The table name prefix.
		 *
		 */
	Public Function __construct($hostname, $username, $password, $dbname, $prefix=NULL, $driver='mysql') {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->dbname = $dbname;
		$this->prefix = $prefix;
		$this->driver = $driver;
	}





		/*
		 * GETTER METHODS
		 */





		/**
		 *
		 * Gets the hostname.
		 * @return string The hostname
		 *
		 */

	Public Function getHostname() { Return $this->hostname; }



		/**
		 *
		 * Gets the username
		 * @return string The user name
		 *
		 */

	Public Function getUsername() { Return $this->username; }



		/**
		 *
		 * Gets the password.
		 * @return string The password
		 *
		 */

	Public Function getPassword() { Return $this->password; }



		/**
		 *
		 * Gets the database name.
		 * @return string The database name
		 *
		 */
	Public Function getDatabaseName() { Return $this->dbname; }



		/**
		 *
		 * Gets the table name prefix.
		 * @return string The table name prefix.
		 *
		 */

	Public Function getPrefix() { Return $this->prefix; }



		/**
		 *
		 * Gets the driver that is to be used for the connection.
		 * @return string The driver.
		 *
		 */

	Public Function getDriver() { Return $this->driver; }



		/**
		 *
		 * Gets the Data Source Name -- short DSN -- for this database connection. This
		 * DNS is for example used by PHP's PDO extension.
		 * @return string The DSN
		 *
		 */
	
	Public Function getDSN() {
		If($this->driver == 'sqlite' || $this->driver == 'sqlite2')
			Return $this->driver.':'.$this->getDatabaseName();
		ElseIf($this->driver == 'pgsql')
			Return sprintf ( "pgsql:dbname=%s;user=%s;password=%s;host=%s",
				$this->getDatabaseName(), $this->getUsername(), $this->getPassword(), $this->getHostname() );
		Else Return $this->driver.':host='.$this->getHostname().';dbname='.$this->getDatabaseName();
	}

}

?>
