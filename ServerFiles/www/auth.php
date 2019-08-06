<?php
//=======================================================================================
// auth.php
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
require_once('../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Script handler for requests of the kind
// "https://www.movescount.com/auth?client_id=XXX"

$html = <<<__HTML__
<!DOCTYPE html>
<html>
<head>
<style>
body {
	font-family: "Verdana", sans-serif;
	font-size: 150%;
}
input {
	font-family: "Verdana", sans-serif;
	font-size: 120%;
	padding: 10px 30px 10px 30px;
	border: none;
	color: white;
	background-color: red;
}
</style>
</head>
<body>
  <h1>Moveslink Authentication</h1>
  <form onsubmit="window.location.replace('movescount://authorized?email=Ambit%40User&userkey=1a2b3c4d-abcd-4567-9876-01a2b3c4d5e6'); return false;">
    <p>Please click the "Login" button to continue!</p>
    <p><input type="submit" value="Login" /></p>
  </form>
</body>
</html>
__HTML__;

Output::HTML($html);

?>
