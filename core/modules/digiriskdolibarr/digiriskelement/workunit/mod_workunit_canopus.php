<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville        <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur         <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin               <regis.houssin@inodbox.com>
 * Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

/**
 * \file       htdocs/custom/digiriskdolibarr/core/modules/digiriskdolibarr/digiriskelement/mod_workunit_canopus.php
 * \ingroup    digiriskelement
 * \brief      File that contains the numbering module rules Canopus
 */

require_once __DIR__ . '/../modules_digiriskelement.php';

/**
 * Class of file that contains the numbering module rules Canopus
 */
class mod_workunit_canopus extends ModeleNumRefDigiriskElement
{
	/**
	 * Dolibarr version of the loaded document
	 * @var string
	 */
	public $version = 'dolibarr'; // 'development', 'experimental', 'dolibarr'

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string Nom du modele
	 * @deprecated
	 * @see $name
	 */
	public $nom = 'Canopus';

	/**
	 * @var string model name
	 */
	public $name = 'Canopus';

	public $prefix = "";



	/**
	 *  Return description of module
	 *
	 *  @return     string      Texte descripif
	 */
	public function info()
	{

		global $conf, $langs, $db;

		$langs->load("bills");

		$form = new Form($db);

		$texte = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$texte .= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte .= '<input type="hidden" name="token" value="'.newToken().'">';
		$texte .= '<input type="hidden" name="action" value="updateMask">';
		$texte .= '<input type="hidden" name="mask" value="DIGIRISKDOLIBARR_WORKUNIT_CANOPUS_ADDON">';
		$texte .= '<table class="nobordernopadding" width="100%">';

		$tooltip = $langs->trans("GenericMaskCodes", $langs->transnoentities("DigiriskElement"), $langs->transnoentities("DigiriskElement"));
		$tooltip .= $langs->trans("GenericMaskCodes2");
		$tooltip .= $langs->trans("GenericMaskCodes3");
		$tooltip .= $langs->trans("GenericMaskCodes4a", $langs->transnoentities("DigiriskElement"), $langs->transnoentities("DigiriskElement"));
		$tooltip .= $langs->trans("GenericMaskCodes5");

		// Parametrage du prefix
		$texte .= '<tr><td>'.$langs->trans("Mask").':</td>';
		$texte .= '<td class="right">'.$form->textwithpicto('<input type="text" class="flat minwidth175" name="addon_value" value="'.$conf->global->DIGIRISKDOLIBARR_WORKUNIT_CANOPUS_ADDON.'">', $tooltip, 1, 1).'</td>';

		$texte .= '<td class="left" rowspan="2">&nbsp; <input type="submit" class="button button-edit" name="Button"value="'.$langs->trans("Modify").'"></td>';

		$texte .= '</tr>';

		$texte .= '</table>';
		$texte .= '</form>';

		return $texte;
	}

	/**
	 *  Return an example of numbering
	 *
	 *  @return     string      Example
	 */
	public function getExample()
	{
		global $conf, $langs, $mysoc;

		$old_code_client = $mysoc->code_client;
		$old_code_type = $mysoc->typent_code;
		$mysoc->code_client = 'CCCCCCCCCC';
		$mysoc->typent_code = 'TTTTTTTTTT';
		$numExample = $this->getNextValue($mysoc, '');
		$mysoc->code_client = $old_code_client;
		$mysoc->typent_code = $old_code_type;

		if (!$numExample) {
			$numExample = 'NotConfigured';
		}
		return $numExample;
	}

	/**
	 *  Return next value
	 *
	 *  @param	Societe		$objsoc     Object third party
	 * 	@param	Propal		$digiriskelement		Object commercial proposal
	 *  @return string      			Value if OK, 0 if KO
	 */
	public function getNextValue($digiriskelement)
	{
		global $db, $conf;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

		// On defini critere recherche compteur
		$mask = $conf->global->DIGIRISKDOLIBARR_WORKUNIT_CANOPUS_ADDON;

		if (!$mask) {
			$this->error = 'NotConfigured';
			return 0;
		}

		// Get entities
		$entity = getEntity('digiriskdolibarr_digiriskelement', 1, $digiriskelement);

		$date = $digiriskelement->date;

		$numFinal = get_next_value($db, $mask, 'digiriskdolibarr_digiriskelement', 'ref', '', $objsoc, $date, 'next', false, null, $entity);

		return  $numFinal;
	}
}
