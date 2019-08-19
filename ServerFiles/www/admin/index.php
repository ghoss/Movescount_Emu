<?php
//=======================================================================================
// index.php
//
// Movescount Emulation Project (MEP)
// Created: 1. August 2019 at 06:31:30 CEST
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
require_once('../../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Main admin page

$html = <<<__HTML__
<!DOCTYPE html>
<html>
<head>
	<title>MovesCount Emulation Server</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<h1>MovesCount Emulation Server</h1>
__HTML__;

// Enumerate synchronized devices in database
$rows = DB::query("SELECT DISTINCT serial FROM settings");
$count = 0;
while ($row = $rows->fetchArray(SQLITE3_ASSOC))
{
	$serial = $row['serial'];
	$html .= <<<__HTML__
		<h2>Device # $serial</h2>
		<ul>
			<li class="listitem"><a href="moves.php/$serial">Manage Moves</a></li>
			<li class="listitem"><a href="settings.php/$serial">Change Watch Settings</a></li>
			<li class="listitem"><a href="custom.php/$serial">Configure Custom Modes</a></li>
		</ul>
__HTML__;
	$count ++;
}

if ($count == 0)
{
	$html .= "<p>No devices found in database. Please synchronize your watch via MovesLink first.</p>";
}

$html .= <<<__HTML__
</body>
</html>
__HTML__;

Output::HTML($html);

?>