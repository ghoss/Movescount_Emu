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

// Get schema definitions for watch settings
require_once(DIR_DATA . 'schema-settings');

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
	// Write changed values into database
	unset($PARAM['submit']);
	$settings = json_encode($PARAM);
	$sql = DB::prepare(
		"UPDATE settings SET settings=:vSet,serverChange=1 WHERE serial=:vSerial"
	);
	DB::bind($sql, ':vSet', $settings);
	DB::bind($sql, ':vSerial', $serial);
	DB::execStatement($sql);
}

// Display current watch settings
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
	<h2>Watch Settings For # $serial</h2>
__HTML__;

// Retrieve settings from database
$sql = sprintf("SELECT settings FROM settings WHERE serial='%s'", DB::escape($serial));
$settings = DB::query($sql, true, false);
if (! is_null($settings))
{
	// Enumerate all fields of setting record
	$settings = json_decode($settings, true);
	$html .= "<form method='POST' onsubmit='alert(\"Please synchronize your watch again later to activate the changed settings.\")'><table>";
	foreach ($settings as $key => $val)
	{
		// Set the field names, values and descriptions for the input form
		if (isset($SETTING_SCHEMA[$key]))
		{
			$schema = $SETTING_SCHEMA[$key];
			$title = $schema['name'];
		}
		else
		{
			$title = "$key [???]";
		}
		$unit = isset($schema['unit']) ? '['.$schema['unit'].']' : '';
		
		$html .= "<tr><td>$title</td><td>";
		if ($schema['list'] == 0)
		{
			// Standard input field
			$html .= "<input type='text' name='$key' value='$val' size='10' /></td><td>$unit</td></tr>";
		}
		else
		{
			// Dropdown list
			$html .= "<select name='$key'>";
			foreach ($schema['options'] as $opt => $optname)
			{
				$selected = ($opt == $val) ? "selected" : "";
				$html .= "<option value='$opt' $selected>$optname</option>";
			}
			$html .= "</select>";
		}
		$html .= "</td></tr>";
	}
	$html .= "</table><p><input type='submit' name='submit' value='Save Settings' /></p></form>";
}
else
{
	$html .= "<p>Database contains no settings from this device.</p>";
}
	
$html .= <<<__HTML__
	<footer>
		<p>Back to <a href="../index.php">Main Menu</a></p>
	</footer>
</body>
</html>
__HTML__;

Output::HTML($html);

?>