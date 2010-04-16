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
	 * Interface to retrieve import files using a FTP connection. Public interfaces are
	 * defined by the Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface
	 * class.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @copyright  2010 Martin Helmich, Mittwald CM Service GmbH & Co KG
	 * @package    MmForumImport
	 * @subpackage Service_FileInterface
	 * @version    $Id$
	 *
	 */

Class Tx_MmForumImport_Domain_Service_FileInterface_FTPFileInterface Extends Tx_MmForumImport_Domain_Service_FileInterface_AbstractFileInterface {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * The FTP working directory
		 * @var string
		 */
	Private $path = './';

		/**
		 * The FTP hostname
		 * @var string
		 */
	Private $hostname;

		/**
		 * The FTP username
		 * @var string
		 */
	Private $username;

		/**
		 * The FTP password
		 * @var string
		 */
	Private $password;

		/**
		 * The FTP connection
		 * @var resource
		 */
	Private $connection;





		/*
		 * CONSTRUCTOR
		 */





		/**
		 *
		 * Creates a new FTP interface.
		 *
		 * @param string $hostname The hostname
		 * @param string $username The username
		 * @param string $password The password
		 * @param string $path     The working directory
		 *
		 */

	Public Function __construct($hostname, $username, $password, $path) {
		$this->path = $path;

		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;

			# Try to create a connection and to log in. Throw an exception if any of
			# these operations does not succeed.
		If(!$this->connection = @ftp_connect($this->hostname))
			Throw New Exception("Could not connect to FTP host $hostname!");
		If(!@ftp_login($this->connection, $this->username, $this->password))
			Throw New Exception("Username and password were not accepted by the FTP server!");

		ftp_chdir($this->connection, $path);
	}





		/*
		 * FILE INTERFACE METHODS
		 */





		/**
		 *
		 * Retrieves a remote file via the FTP connection.
		 *
		 * @param string $source      The remote filename
		 * @param string $destination The local target filename
		 *
		 */

	Public Function retrieveFile($source, $destination) {
		$this->createParentDirectory($this->typo3path.$destination);
		If(!ftp_get($this->connection, $this->typo3path.$destination, $source))
			Throw New Tx_MmForumImport_Domain_Service_FileInterface_FileNotFoundException ("The file {$this->path}{$source} does not exist!");
	}

}

?>