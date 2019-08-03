<?php
//=======================================================================================
// Request.php
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

class Request
{
	private static $instance;
	private static $data;
	
	//-----------------------------------------------------------------------------------						
	// initialize()
	// Class Initialization
	//-----------------------------------------------------------------------------------						
	
	public static function initialize()
	{
		// Only execute initialization routine once
		if (! isset(self::$instance))
		{		
			// Get extra path info after script name
			$object = isset($_SERVER['PATH_INFO']) ? 
				substr($_SERVER['PATH_INFO'], 1) : '';
			
			// Split path into tags delimited by '/'
			$taglist = explode('/', $object);
			
			// Remove empty tags from array and sanitize the non-empty ones
			foreach ($taglist as $key => $val)
			{
				$val = self::sanitize($val);
				if ($val == '')
				{
					unset($taglist[$key]);
				}
				else
				{
					$taglist[$key] = $val;
				}
			}
			self::$data['pathInfo'] = $taglist;
			
			// Get optional script params, if any
			self::$data['param'] = $_REQUEST;
			
			// Get script basename
			$base = $_SERVER['SCRIPT_NAME'];
			self::$data['baseURL'] = $base;			
			
			self::$instance = true;
		}
	}
	
	
	//-----------------------------------------------------------------------------------						
	// sanitize()
	// Removes special characters from a URI or string destined to be a URI
	//-----------------------------------------------------------------------------------						
	
	public static function sanitize($str)
	{
		$str = str_replace(array('[\', \']'), '', $str);
		$str = preg_replace('/\[.*\]/U', '', $str);
		$str = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $str);
		$str = preg_replace(array('/[^a-z0-9äöüÄÖÜß:.]/i', '/[-]+/') , '-', $str);
		
		return trim($str, '-');		
	}
	

	//-----------------------------------------------------------------------------------						
	// get()
	// Getter for private class variables
	//-----------------------------------------------------------------------------------						
	
	public static function get($var)
	{
		if (isset(self::$data[$var]))
		{
			return self::$data[$var];
		}
		else
		{
			trigger_error(_("Invalid call") . ": get('$var')");
		}
	}
}

?>