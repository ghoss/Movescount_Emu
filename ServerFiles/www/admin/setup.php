<?php
//=======================================================================================
// setup.php
//
// Movescount Emulation Project (MEP)
// Created: 3. August 2019 at 16:20:23 CEST
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
require_once('../../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Database setup script

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

// SQL table creation
$sql = <<<__SQL__
CREATE TABLE 'poi' ('serial' TEXT NOT NULL, 'name' TEXT NOT NULL, 'data' TEXT, 'fromWatch' BOOLEAN, PRIMARY KEY ('serial', 'name'));
CREATE TABLE 'moves' ('serial' TEXT NOT NULL, 'moveId' TEXT NOT NULL, 'startTime' DATETIME, 'data' TEXT, PRIMARY KEY ('serial', 'moveId'));
CREATE TABLE 'settings' ('serial' TEXT PRIMARY KEY NOT NULL, 'lastSync' DATETIME, 'settings' TEXT, 'customModes' TEXT);
__SQL__;

DB::exec($sql);

$html .= <<<__HTML__
	<p>Database tables created.</p>
	<footer>
		<p>Proceed to <a href="index.php">Main Menu</a></p>
	</footer>
</body>
</html>
__HTML__;

Output::HTML($html);

?>