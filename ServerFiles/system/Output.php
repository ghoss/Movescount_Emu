<?php
//=======================================================================================
// Output.php
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

class Output
{
	private static $instance;
	
	//-----------------------------------------------------------------------------------
	// initialize()
	// Class Initialization
	//-----------------------------------------------------------------------------------
	
	public static function initialize()
	{
		// Only execute initialization routine once
		if (! isset(self::$instance))
		{
			// Open database
			self::$instance = true;
		}
	}
	
	
	//-----------------------------------------------------------------------------------						
	// JSON()
	// Outputs the specified array as a JSON response with the correct HTTP header.
	//-----------------------------------------------------------------------------------						
	
	public static function JSON(&$a)
	{
		header('Content-type: application/json');
		echo json_encode($a);
	}
	
	
	//-----------------------------------------------------------------------------------						
	// HTML()
	// Outputs the specified string as HTML.
	//-----------------------------------------------------------------------------------						
	
	public static function HTML(&$a)
	{
		header('Content-type: text/html');
		echo $a;
	}
	
	//-----------------------------------------------------------------------------------						
	// Empty()
	// Creates empty output with null content-type header.
	//-----------------------------------------------------------------------------------						
	
	public static function Empty()
	{
		header('Content-type: null');
		header_remove('Content-type');
	}
	
	//-----------------------------------------------------------------------------------						
	// File()
	// Outputs a file attachment for download.
	//-----------------------------------------------------------------------------------						
	
	public static function File($filename, &$data)
	{
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($data));
		echo $data;
	}
}

?>