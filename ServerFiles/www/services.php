<?php
//=======================================================================================
// services.php
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
require_once('../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Script handler for requests of the kind
// "https://www.movescount.com/services/{UserAuthenticated,GetAppInfo,AcceptApp}"

$PATH = Request::get('pathInfo');
$req = $PATH[0];
$data = [ 'Error' => null ];

if ($req == 'UserAuthenticated')
{
	$value = '/overview';
}
elseif ($req == 'GetAppInfo')
{
	$value = [
		'Icon' => null,
		'Name' => 'MEP',
		'RedirectUrl' => null,
		'Status' => 0,
		'__type' => 'MEP.UI.Site.Services+AppInfo'
	];
}
elseif ($req == 'AcceptApp')
{
	$value = [
		'RedirectUrl' => null
	];
}
else
{
	trigger_error("Invalid request ($req)");
}

$data['Value'] = $value;
$result = [ 'd' => $data ];
Output::JSON($result);

?>
