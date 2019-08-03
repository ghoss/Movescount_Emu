<?php
//=======================================================================================
// settings.php
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
// Git repository home: <https://github.com/ghoss/MEP>
//=======================================================================================

// Register autoloader for MEP classes
require_once('../../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Get serial of watch
$PATH = Request::get('pathInfo');
if (isset($PATH[0]))
{
	$serial = $PATH[0];
}
else
{
	trigger_error("Invalid device serial#");
}

// Display watch settings

$html = <<<__HTML__
<!DOCTYPE html>
<html>
<head>
	<title>MovesCount Emulation Server</title>
	<link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
	<h1>MovesCount Emulation Server</h1>
	<h2>Watch Settings For # $serial</h2>
	<p>This feature has not been implemented yet. Stay tuned ;)</p>
__HTML__;

$html .= <<<__HTML__
	<footer>
		<p>Back to <a href="../index.php">Main Menu</a></p>
	</footer>
</body>
</html>
__HTML__;

Output::HTML($html);

?>