<?php
//=======================================================================================
// moves.php
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

$PATH = Request::get('pathInfo');
$PARAM = Request::get('param');
$baseURL = Request::get('baseURL');

// Get serial of watch
if (isset($PATH[0]))
{
	$serial = $PATH[0];
}
else
{
	trigger_error("Invalid device serial#");
}

if (isset($PATH[1]))
{
	// Handle Delete and Download options for individual moves
	$moveId = $PATH[1];
	$redir = false;
	
	if (isset($PARAM['download']))
	{
		convertMoveToGPX($serial, $moveId);
	}
	elseif (isset($PARAM['delete']))
	{
		deleteMove($serial, $moveId);
		$redir = true;
	}
	else
	{
		trigger_error("Invalid option");
	}
	
	if ($redir)
	{
		// Redirect to selection list
		header("Location: $baseURL/$serial");
		Output::Empty();
	}
}
else
{
	// No move specified; show selection list of all moves
	$html = <<<__HTML__
	<!DOCTYPE html>
	<html>
	<head>
		<title>MovesCount Emulation Server</title>
		<link rel="stylesheet" type="text/css" href="../styles.css">
	</head>
	<body>
		<h1>MovesCount Emulation Server</h1>
		<h2>Manage Moves</h2>
		<p>Device: # $serial</p>
__HTML__;

	// Enumerate synchronized devices in database
	$sql = sprintf("SELECT moveId FROM moves WHERE serial='%s' ORDER BY moveId",
		DB::escape($serial));
	$rows = DB::query($sql);
	$count = 0;
	$list = "";
	while ($row = $rows->fetchArray(SQLITE3_ASSOC))
	{
		// Get filename and URL of move
		$name = $row['moveId'];
		$url = "$serial/$name";
	
		// Compose menu list entry
		$list .= "<li class='listitem'>$name (<a href='$url?download'>Download</a> | <a href='$url?delete' onclick='return confirm(\"Do you want to delete this move?\");'>Delete</a>)</li>";
		$count ++;
	}
	if ($count == 0)
	{
		$html .= "<p>Database contains no moves from this device.</p>";
	}
	else
	{
		$html .= "<ol>$list</ol>";
	}

	$html .= <<<__HTML__
		<footer>
			<p>Back to <a href="../index.php">Main Menu</a></p>
		</footer>
	</body>
	</html>
__HTML__;

	Output::HTML($html);
}


//---------------------------------------------------------------------------------------
// convertMoveToGPX
//
// Parses data of the move identified by (serial,moveId) and converts it to GPX format.
//
// Unfortunately, the watch measures altitude vs. longitude/latitude independently
// (presumably in two circuits, one barometric and one GPS-based), so the samples from
// both (asynchronous!) circuits must be consolidated in order to get the results based
// on which a GPX file can be generated. 
//---------------------------------------------------------------------------------------

function convertMoveToGPX($serial, $moveId)
{
	// Retrieve move data from the database
	$sql = sprintf("SELECT data FROM moves WHERE serial='%s' AND moveId='%s'",
		DB::escape($serial), DB::escape($moveId)
	);
	$data = DB::query($sql, true, false);
	$data = json_decode($data, true);
	
	// Step 1: Uncompress and parse the track points (BASE64 + GZIP + JSON)
	if (isset($data['Track']))
	{
		$track = base64_decode($data['Track']['CompressedTrackPoints']);
		$track = json_decode(gzdecode($track), true);
	}

	// Step 2: Uncompress and parse the sample set (also BASE64 + GZIP + JSON)
	if (isset($data['Samples']))
	{
		$samples = base64_decode($data['Samples']['CompressedSampleSets']);
		$samples = json_decode(gzdecode($samples), true);
	}
	
	// Step 3: Merge the data from track points and samples
	unset($data);
	define('SKIP_NONE', 0);
	define('SKIP_TRACK', 1);
	define('SKIP_SAMPLES', 2);
	define('SKIP_BOTH', 3);

	$idx_t = $idx_s = 0;
	$max_t = count($track);
	$max_s = count($samples);
	$skip = $lastskip = SKIP_NONE;
	$result = [];
	
	while (($idx_t < $max_t) && ($idx_s < $max_s))
	{
		// Get first elements of track and sample arrays
		$track_ele = $track[$idx_t];
		$sample_ele = $samples[$idx_s];
		
		// Only consider valid samples and track points
		if ((! isset($sample_ele['Altitude'])) || ($sample_ele['Altitude'] == 0))
		{
			$idx_s ++;
			continue;
		}
		
		// Get timestamps of each element
		$track_tm = $track_ele['LocalTime'];
		$sample_tm = $sample_ele['LocalTime'];

		// Find closest adjacent track and sample elements by skipping ahead each array
		// in turns
		$lastskip = $skip;
		if ($track_tm < $sample_tm)
		{
			$idx_t ++;
			$skip = SKIP_TRACK;
		}
		elseif ($sample_tm < $track_tm)
		{
			$idx_s ++;
			$skip = SKIP_SAMPLES;
		}
		else
		{
			$idx_t ++;
			$idx_s ++;
			$skip = SKIP_BOTH;
		}
		
		// We have encountered a close track/sample element, so generate one output
		// datapoint from both
		if (($skip != $lastskip) && ($skip != SKIP_NONE))
		{
			if ($skip == SKIP_SAMPLES)
			{
				$idx_t ++;
			}
			elseif ($skip == SKIP_TRACK)
			{
				$idx_s ++;
			}
			$result[] = [
				'time'	=> $track_tm,
				'ele'	=> $sample_ele['Altitude'],
				'lat'	=> $track_ele['Latitude'],
				'lon'	=> $track_ele['Longitude']
			];
		}
	}
	
	// Generate GPX file
	unset($track);
	unset($samples);
	$gpx = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?><gpx xmlns="http://www.topografix.com/GPX/1/1" creator="Suunto Ambit" version="1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd"><trk><name>' . $moveId . '</name><trkseg>';
	
	foreach ($result as $point)
	{
		$gpx .= sprintf('<trkpt lat="%s" lon="%s"><ele>%s</ele><time>%s</time></trkpt>',
			$point['lat'], $point['lon'],
			$point['ele'], $point['time']
		);
	}
	
	$gpx .= '</trkseg></trk></gpx>';
	$filename = $moveId . ".gpx";
	Output::File($filename, $gpx);
}


//---------------------------------------------------------------------------------------
// deleteMove
//
// Deletes the move record identified by (serial,moveId) from the database.
//---------------------------------------------------------------------------------------

function deleteMove($serial, $moveId)
{
	$sql = sprintf("DELETE FROM moves WHERE serial='%s' AND moveId='%s'",
		DB::escape($serial), DB::escape($moveId)
	);
	DB::exec($sql);
}

?>