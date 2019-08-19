<?php
//=======================================================================================
// userdevices.php
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

// Handle a request with the format
// https://uiservices.movescount.com/userdevices/YYYYY?appkey=xxx"

$PATH = Request::get('pathInfo');
$ARG = Request::get('param');

// The device's serial number immediately follows the "userdevices" URI
$serial = $PATH[0];

// Process request depending on selected operation mode
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
	// Mode A: Receive settings from client and store them in database
	StoreSettingsOrWP($serial);
	Output::Empty();
}
elseif (isset($ARG['onlychangedsettings']))
{
	// Mode B: Return abbreviated settings with waypoints/POIs
	$data = ReturnSettingsAndWP($serial, $ARG['model'], $ARG['eswversion'], false);
	Output::JSON($data);
}
else
{
	// Mode C: Return settings, waypoints, and custom groups
	$data = ReturnSettingsAndWP($serial, $ARG['model'], $ARG['eswversion'], true);
	Output::JSON($data);
}


//---------------------------------------------------------------------------------------
// StoreSettingsOrWP
//
// Handle a PUT request from the client which transmits watch settings or waypoints.
// No HTTP response is generated in either case.
//---------------------------------------------------------------------------------------

function StoreSettingsOrWP($serial)
{
	// Fetch the data in the PUT request
	$putdata = file_get_contents('php://input');
	$data = json_decode($putdata, true);
	$db_serial = DB::escape($serial);
	
	// Create a database entry for the watch serial in case it doesn't exist yet
	$sql = sprintf("INSERT INTO settings VALUES ('%s', 'null', 'null', '0')", $db_serial);
	try	{ DB::exec($sql); } catch (Exception $e) {};
			
	if (isset($data['Settings']))
	{
		// Store watch settings if supplied

		$sql = sprintf("SELECT serverChange FROM settings WHERE serial='%s'", $db_serial);
		$serverChange = DB::Query($sql, true, false);
		if ($serverChange == 0)
		{
			// Only update settings in database if not changed locally already
			$sql = DB::prepare(
				"UPDATE settings SET settings=:vSet WHERE serial=:vSerial"
			);
			DB::bind($sql, ':vSet', json_encode($data['Settings']));
			DB::bind($sql, ':vSerial', $serial);
			DB::execStatement($sql);
		}
		else
		{
			// Local update, so ignore data sent from watch; reset flag
			$sql = sprintf("UPDATE settings SET serverChange=0 WHERE serial='%s'",
				$db_serial);
			DB::Exec($sql);
		}

	}
	elseif (isset($data['Waypoints']))
	{
		// Store watch waypoints/POIs if supplied
		
		// Delete all previous waypoints originating from watch
		$sql = sprintf("DELETE FROM poi WHERE serial='%s' and fromWatch=1", $db_serial);
		DB::exec($sql);
		
		// Insert 
		$sql = DB::prepare("INSERT INTO poi VALUES (:vSerial,:vName,:vData,1)");
		foreach ($data['Waypoints'] as $wp)
		{
			DB::bind($sql, ':vSerial', $serial);
			DB::bind($sql, ':vName', $wp['Name']);
			unset($wp['Name']);
			DB::bind($sql, ':vData', json_encode($wp));
			try { DB::execStatement($sql); } catch (Exception $e) {};
		}
	}
}


//---------------------------------------------------------------------------------------
// ReturnSettingsAndWP
//
// Retrieve abbreviated settings and waypoints from the database.
//---------------------------------------------------------------------------------------

function ReturnSettingsAndWP($serial, $model, $version, $fullSettings)
{
	if (true)
	{
		$data = [
			"DeviceDisplayName" => 'Suunto Ambit (old)',
			"DeviceName" => $model,
			"DeviceURI" => "devices/$model",
			"FirmwareURI" => "devices/$model/$version",
			"FirmwareVersion" => $version,
			"HardwareURI" => "devices/$model/$version",
			"HardwareVersion" => $version,
			"Maps" => null,
			"RouteURIs" => null,
			"SelfURI" => "userdevices/$serial",
			"SerialNumber" => $serial,
			"SubscribedFwReleaseType" => null
		];
		$db_serial = DB::escape($serial);
		
		// Download last sync date from settings
		$sql = sprintf(
			"SELECT lastSync,settings FROM settings WHERE serial='%s'", $db_serial
		);
		$row = DB::query($sql, true);
		if (isset($row) && isset($row['lastSync']))
		{
			$data['LastSynchedMoveStartTime'] = $row['lastSync'];
			$settings = json_decode($row['settings'], true);
		}
		else
		{
			$data['LastSynchedMoveStartTime'] = null;
			$settings = null;
		}
		
		// Download waypoints
		$sql = sprintf("SELECT * FROM poi WHERE serial='%s'", $db_serial);
		$rows = DB::query($sql);
		$pois = [];
		while ($row = $rows->fetchArray(SQLITE3_ASSOC))
		{
			$rowdata = json_decode($row['data'], true);
			$rowdata['Name'] = $row['name'];
			array_push($pois, $rowdata);
		}
		$data['POIs'] = $data['Waypoints'] = $pois;
		
		// Retrieve full settings if requested
		if ($fullSettings)
		{
			$settings['CustomModeGroups'] = null;
			$settings['CustomModes'] = null;
			$data['Settings'] = $settings;
		}
		else
		{
			$data['Settings'] = null;
		}
		
		return $data;
	}
	else
	{
		trigger_error("Invalid model ($model)");
	}
}

?>
