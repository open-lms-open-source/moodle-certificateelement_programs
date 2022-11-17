<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Program fields.
 *
 * @package    certificateelement_programs
 * @copyright  2022 Open LMS (https://www.openlms.net/)
 * @author     Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** @var stdClass $plugin */

$plugin->version   = 2022111500;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2021051704;        // Requires this Moodle version.
$plugin->component = 'certificateelement_programs';
$plugin->maturity  = MATURITY_RC;
$plugin->release   = 'v1.0.7.1+';
$plugin->supported = [311, 400];

$plugin->dependencies = ['enrol_programs' => 2022111500, 'tool_certificate' => 2022031630];
