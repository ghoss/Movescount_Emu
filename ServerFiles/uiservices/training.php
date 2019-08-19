<?php
//=======================================================================================
// training.php
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

// Register autoloader for MEP classes
require_once('../config');
require_once(DIR_SYS . 'Bootstrap.php');
Bootstrap::initialize();

// Script handler for requests of the kind
// "https://uiservices.movescount.com/training/plannedtrainingprogram/plannedtrainingpr
//     ogrammo?"
//
// Planned trainings are not implemented (yet). We therefore always respond with a null
// array.

$result = [];
Output::JSON($result);

?>
