<?php
/* Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    digiriskdolibarr/lib/digiriskdolibarr.lib.php
 * \ingroup digiriskdolibarr
 * \brief   Library files with common functions for Digiriskdolibarr
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function digiriskdolibarrAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("digiriskdolibarr@digiriskdolibarr");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/legaldisplay.php", 1);
	$head[$h][1] = $langs->trans("LegalDisplay");
	$head[$h][2] = 'legaldisplay';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/informationssharing.php", 1);
	$head[$h][1] = $langs->trans("InformationsSharing");
	$head[$h][2] = 'informationssharing';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/firepermit.php", 1);
	$head[$h][1] = $langs->trans("FirePermit");
	$head[$h][2] = 'firepermit';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/preventionplan.php", 1);
	$head[$h][1] = $langs->trans("PreventionPlan");
	$head[$h][2] = 'preventionplan';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/groupment.php", 1);
	$head[$h][1] = $langs->trans("Groupment");
	$head[$h][2] = 'groupment';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/workunit.php", 1);
	$head[$h][1] = $langs->trans("WorkUnit");
	$head[$h][2] = 'workunit';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/risk.php", 1);
	$head[$h][1] = $langs->trans("Risks");
	$head[$h][2] = 'risk';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/signalisation.php", 1);
	$head[$h][1] = $langs->trans("Signalisations");
	$head[$h][2] = 'signalisation';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/listingrisksphoto.php", 1);
	$head[$h][1] = $langs->trans("ListingRisksPhoto");
	$head[$h][2] = 'listingrisksphoto';
	$h++;

	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/listingrisksaction.php", 1);
	$head[$h][1] = $langs->trans("ListingRisksAction");
	$head[$h][2] = 'listingrisksaction';
	$h++;

//	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/evaluations.php", 1);
//	$head[$h][1] = $langs->trans("Evaluations");
//	$head[$h][2] = 'evaluation';
//	$h++;

//	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/project.php", 1);
//	$head[$h][1] = $langs->trans("Projects");
//	$head[$h][2] = 'projet';
//	$h++;

//	$head[$h][0] = dol_buildpath("/digiriskdolibarr/admin/accronym.php", 1);
//	$head[$h][1] = $langs->trans("Accronym");
//	$head[$h][2] = 'accronym';
//	$h++;
//
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@digiriskdolibarr:/digiriskdolibarr/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@digiriskdolibarr:/digiriskdolibarr/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'digiriskdolibarr');

	return $head;
}
