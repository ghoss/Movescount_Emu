<?php
//=======================================================================================
// custom.php
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
$PARAM = Request::get('param');
if (isset($PATH[0]))
{
	$serial = $PATH[0];
}
else
{
	trigger_error("Invalid device serial#");
}

if (isset($PARAM['submit']))
{
	$res = "<pre>".print_r($PARAM,true)."</pre>";
	Output::HTML($res); exit();
	// Write changed values into database
// 	unset($PARAM['submit']);
// 	$settings = json_encode($PARAM);
// 	$sql = DB::prepare(
// 		"UPDATE settings SET settings=:vSet,serverChange=1 WHERE serial=:vSerial"
// 	);
// 	DB::bind($sql, ':vSet', $settings);
// 	DB::bind($sql, ':vSerial', $serial);
// 	DB::execStatement($sql);
}

// Display custom mode settings
$html = <<<__HTML__
<!DOCTYPE html>
<html>
<head>
	<title>MovesCount Emulation Server</title>
	<link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
	<h1>MovesCount Emulation Server</h1>
	<p><b>NOTE:</b> Very limited input validation performed only. Please be mindful of what you enter and change below!</p>
	<h2>Custom Modes For # $serial</h2>
	<form method='POST'>
__HTML__;

// Retrieve custom modes from database
$sql = sprintf("SELECT data FROM custom WHERE serial='%s'", DB::escape($serial));
$rows = DB::query($sql);
$modelist = [];
while ($row = $rows->fetchArray(SQLITE3_ASSOC))
{
	$rowdata = json_decode($row['data'], true);
	$modelist[] = [
		'data' => $rowdata,
		'title' => sprintf('Custom Mode "%s"', "Test")
	];
}

if (count($modelist) == 0)
{
	$html .= "<p>No custom modes found for this device.</p>";
}

// Add a default empty mode for new entries
$modelist[] = [
	'data' => [
		'field1' => "",
		'field2' => ""
	],
	'title' => 'Add A New Custom Mode'
];

// Generate HTML list of all modes
foreach ($modelist as $mode)
{
	$html .= "<hr /><h3>" . $mode['title'] . "</h3><table>";
	foreach ($mode['data'] as $key => $val)
	{
		$html .= "<tr><td>$key</td><td><input type='text' name='".$key."[]' value='$val' /></td></tr>";
	}
	$html .= "</table><p><input type='button' name='delete' value='Delete This Mode' /></p><hr />";
}
$html .= "<p><input type='submit' name='submit' value='Save Changes To Custom Modes' /></p>";
		
$html .= <<<__HTML__
	</form>
	<footer>
		<p>Back to <a href="../index.php">Main Menu</a></p>
	</footer>
</body>
</html>
__HTML__;

Output::HTML($html);

?>