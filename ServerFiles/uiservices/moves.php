<?php
//=======================================================================================
// moves.php
//
// Movescount Emulation Project (MEP)
// Created: 28. July 2019 at 13:03:33 CEST
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
// Git repository home: <https://github.com/ghoss/Movescount_Emu>
//=======================================================================================

// Register autoloader for MEP classes
require_once('../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Script handler for requests of the kind
// "https://uiservices.movescount.com/moves/?appkey=XXX"

$PATH = Request::get('pathInfo');

if (isset($PATH[0]))
{
	// Retrieve specified move
	$move = $PATH[0];
	RetrieveMove($move);
}
else
{
	// The client provides the move in a POST request
	$postdata = file_get_contents('php://input');
	$data = json_decode($postdata, true);
	StoreMove($data);
}


//---------------------------------------------------------------------------------------
// StoreMove
//
// Stores the provided move in the database.
//---------------------------------------------------------------------------------------

function StoreMove($data)
{
	$serial = $data['DeviceSerialNumber'];
	$moveID = $startTime = $data['LocalStartTime'];
	$db_serial = DB::escape($serial);
	$db_starttime = DB::escape($startTime);
	
	// Write move to database
	$sql = sprintf("REPLACE INTO moves VALUES ('%s', '%s', '%s', '%s')",
		$db_serial, DB::escape($moveID), 
		$db_starttime, DB::escape(json_encode($data))
	);
	DB::exec($sql);
	
	// Update last sync timestamp
	$sql = sprintf("UPDATE settings SET lastSync='%s' WHERE serial='%s'",
		$db_starttime, $db_serial
	);
	DB::exec($sql);
	
	// Return result to client
	$result = [
		'MoveID' => $moveID,
		'SelfURI' => "moves/$moveID"
	];
	Output::JSON($result);
}


//---------------------------------------------------------------------------------------
// RetrieveMove
//
// Returns the specified move as a GPX file.
//---------------------------------------------------------------------------------------

function RetrieveMove($move)
{
	trigger_error("Not implemented");
}

?>
