<?php
//=======================================================================================
// DB.php
//
// Movescount Emulation Project (MEP)
// Created: 29. July 2019 at 11:58:41 CEST
//
// Copyright (c) 2019 by Guido Hoss
//
// MEP is free software: you can redistribute it and/or 
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation, either version 3
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public
// License along with this program.  If not, see
// <http://www.gnu.org/licenses/>.
//
// Git repository home: <https://github.com/ghoss/MEP>
//=======================================================================================

class DB
{
	private static $db;
	
	//-----------------------------------------------------------------------------------
	// initialize()
	// Class Initialization
	//-----------------------------------------------------------------------------------
	
	public static function initialize()
	{
		// Only execute initialization routine once
		if (! isset(self::$db))
		{
			// Open database
			self::$db = new SQLite3(DIR_DATA . DBNAME);
			isset(self::$db) or trigger_error(_("Can't open database"));
			
			// Set SQLite "WAL" mode
			self::exec('PRAGMA journal_mode=WAL');
			
			// Enable exceptions
			self::$db->enableExceptions(true);
			
			// Set "busy" timeout when acquiring locks
			self::$db->busyTimeout(60000);
		}
	}
	
	
	//-----------------------------------------------------------------------------------
	// query()
	// Execute SQL query and return results
	//
	// str :		SQL query string
	// oneRow :		Return only first row (true) or everything (false)?
	// allFields :	Return entire first row (true) or only first value (false)?
	//				(Ignored if oneRow = false)
	//-----------------------------------------------------------------------------------
	
	public static function query($str, $oneRow = false, $allFields = true)
	{
		return $oneRow ? 
			self::$db->querySingle($str, $allFields) : 
			self::$db->query($str);
	}


	//-----------------------------------------------------------------------------------
	// prepare()
	// Binds the value of a parameter to a statement variable
	//
	// str :	SQL query parameter to be escaped
	//-----------------------------------------------------------------------------------

	public static function prepare($str)
	{
		return self::$db->prepare($str);
	}


	//-----------------------------------------------------------------------------------
	// bind()
	// Binds the value of a parameter to a statement variable
	//
	// stmt :	SQL statement previously returned by prepare()
	// vr :		Variable to bind to
	// val :	Value to be bound
	//-----------------------------------------------------------------------------------

	public static function bind($stmt, $vr, $val)
	{
		return $stmt->bindValue($vr, $val);
	}


	//-----------------------------------------------------------------------------------
	// escape()
	// Escapes the specified string for a SQL query
	//
	// str :	SQL query parameter to be escaped
	//-----------------------------------------------------------------------------------

	public static function escape($str)
	{
		return SQLite3::escapeString($str);
	}


	//-----------------------------------------------------------------------------------
	// lastError
	// Return the last error message
	//-----------------------------------------------------------------------------------

	public static function lastError()
	{
		return self::$db->lastErrorMsg();
	}
	
	
	//-----------------------------------------------------------------------------------
	// modifiedRows
	// Returns the number of rows changed by the most recent SQL command
	//-----------------------------------------------------------------------------------

	public static function modifiedRows()
	{
		return self::$db->changes();
	}
	
	
	//-----------------------------------------------------------------------------------
	// execStatement()
	// Execute SQL statement
	//
	// stmt :	SQL statement previously prepared with prepare() and bind()
	//-----------------------------------------------------------------------------------

	public static function execStatement($stmt)
	{
		return $stmt->execute();
	}


	//-----------------------------------------------------------------------------------
	// exec()
	// Execute SQL query without results
	//
	// str :	SQL query string
	//-----------------------------------------------------------------------------------

	public static function exec($str)
	{
		return self::$db->exec($str);
	}
}

?>