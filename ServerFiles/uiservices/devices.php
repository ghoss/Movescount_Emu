<?php
//=======================================================================================
// devices.php
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
// Git repository home: <https://github.com/ghoss/MEP>
//=======================================================================================

// Register autoloader for MEP classes
require_once('../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Convert an original request with the format
// "https://uiservices.movescount.com/devices/Duck/75.3.19201?appkey=XXXX"
// into a matching JSON response

$PATH = Request::get('pathInfo');
$ARG = Request::get('param');

if (isset($PATH[0]) && isset($PATH[1]))
{
	$device = $PATH[0];		// e.g. "Duck"
	$version = $PATH[1];	// e.g. "75.3.19201"
	$uploadDate = '2016-06-30T11:46:32ZZZ';
	$latestFirmware = '1.0.0';
		
	// Generate output result
	$result = [
		"DeviceName" => $device,
		"FirmwareUploadDate" => $uploadDate,
		"LatestFirmwareURI" => $device . '/' . $version . '/' . $latestFirmware,
		"LatestFirmwareVersion" => $latestFirmware,
		"Version" => $version
	];
	Output::JSON($result);
}
else
{
	trigger_error('No path info');
}

?>