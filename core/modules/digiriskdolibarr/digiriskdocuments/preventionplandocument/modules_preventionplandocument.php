<?php
/* Copyright (C) 2021 EOXIA <dev@eoxia.com>
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
 *  \file			core/modules/digiriskdolibarr/digiriskdocuments/preventionplandocument/modules_preventionplandocument.php
 *  \ingroup		digiriskdolibarr
 *  \brief			File that contains parent class for preventionplans document models
 */

require_once DOL_DOCUMENT_ROOT . '/core/class/commondocgenerator.class.php';

/**
 *	Parent class for documents models
 */
abstract class ModeleODTPreventionPlanDocument extends CommonDocGenerator
{

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return list of active generation modules
	 *
	 *  @param	DoliDB	$db     			Database handler
	 *  @param int $maxfilenamelength  Max length of value to show
	 *  @return	array						List of templates
	 */
	public static function liste_modeles($db, $maxfilenamelength = 0)
	{
		$type = 'preventionplandocument';

		require_once __DIR__ . '/../../../../../lib/digiriskdolibarr_function.lib.php';
		return getListOfModelsDigirisk($db, $type, $maxfilenamelength);
	}

	/**
	 *  Function to build a document on disk using the generic odt module.
	 *
	 * @param 	PreventionPlanDocument	$object 			Object source to build document
	 * @param 	Translate 				$outputlangs 		Lang output object
	 * @param 	string 					$srctemplatepath 	Full path of source filename for generator using a template file
	 * @param	int						$hidedetails		Do not show line details
	 * @param	int						$hidedesc			Do not show desc
	 * @param	int						$hideref			Do not show ref
	 * @param 	PreventionPlan			$preventionplan		PreventionPlan Object
	 * @return	int                            				1 if OK, <=0 if KO
	 * @throws 	Exception
	 */
	public function write_file($object, $outputlangs, $srctemplatepath, $hidedetails, $hidedesc, $hideref, $preventionplan)
	{
		// phpcs:enable
		global $user, $langs, $conf, $hookmanager, $action, $mysoc;

		if (empty($srctemplatepath)) {
			dol_syslog("doc_preventionplandocument_odt::write_file parameter srctemplatepath empty", LOG_WARNING);
			return -1;
		}

		// Add odtgeneration hook
		if ( ! is_object($hookmanager)) {
			include_once DOL_DOCUMENT_ROOT . '/core/class/hookmanager.class.php';
			$hookmanager = new HookManager($this->db);
		}
		$hookmanager->initHooks(array('odtgeneration'));

		if ( ! is_object($outputlangs)) $outputlangs = $langs;
		$outputlangs->charset_output                 = 'UTF-8';

		$outputlangs->loadLangs(array("main", "dict", "companies", "digiriskdolibarr@digiriskdolibarr"));

		$mod = new $conf->global->DIGIRISKDOLIBARR_PREVENTIONPLANDOCUMENT_ADDON($this->db);
		$ref = $mod->getNextValue($object);

		$object->ref = $ref;
		$id          = $object->create($user, true, $preventionplan);

		$object->fetch($id);

		$dir                                             = $conf->digiriskdolibarr->multidir_output[isset($object->entity) ? $object->entity : 1] . '/preventionplandocument/' . $preventionplan->ref;
		$objectref                                       = dol_sanitizeFileName($ref);
		$tempfilepath = preg_split('/preventionplandocument\//', $srctemplatepath);
		if (preg_match('/specimen/i', $tempfilepath[1])) $dir .= '/specimen';

		if ( ! file_exists($dir)) {
			if (dol_mkdir($dir) < 0) {
				$this->error = $langs->transnoentities("ErrorCanNotCreateDir", $dir);
				return -1;
			}
		}

		if (file_exists($dir)) {
			$filename = preg_split('/preventionplandocument\//', $srctemplatepath);
			$filename = preg_replace('/template_/', '', $filename[1]);
			$societyname = preg_replace('/\./', '_', $conf->global->MAIN_INFO_SOCIETE_NOM);

			$date     = dol_print_date(dol_now(), 'dayxcard');
			$filename = $date . '_' . $preventionplan->ref . '_' . $objectref . '_' . $societyname . ((preg_match('/specimen/i', $tempfilepath[1]) || $preventionplan->status < $preventionplan::STATUS_LOCKED) ? '_specimen' : '_signed') . '.odt';
			$filename = str_replace(' ', '_', $filename);
			$filename = dol_sanitizeFileName($filename);

			$object->last_main_doc = $filename;

			$sql  = "UPDATE " . MAIN_DB_PREFIX . "digiriskdolibarr_digiriskdocuments";
			$sql .= " SET last_main_doc =" . ( ! empty($filename) ? "'" . $this->db->escape($filename) . "'" : 'null');
			$sql .= " WHERE rowid = " . $object->id;

			dol_syslog("admin.lib::Insert last main doc", LOG_DEBUG);
			$this->db->query($sql);
			$file = $dir . '/' . $filename;

			dol_mkdir($conf->digiriskdolibarr->dir_temp);

			// Make substitution
			$substitutionarray = array();
			complete_substitutions_array($substitutionarray, $langs, $object);
			// Call the ODTSubstitution hook
			$parameters = array('file' => $file, 'object' => $object, 'outputlangs' => $outputlangs, 'substitutionarray' => &$substitutionarray);
			$hookmanager->executeHooks('ODTSubstitution', $parameters, $this, $action); // Note that $action and $preventionplan may have been modified by some hooks

			// Open and load template
			require_once ODTPHP_PATH . 'odf.php';
			try {
				$odfHandler = new odf(
					$srctemplatepath,
					array(
						'PATH_TO_TMP'	  => $conf->digiriskdolibarr->dir_temp,
						'ZIP_PROXY'		  => 'PclZipProxy', // PhpZipProxy or PclZipProxy. Got "bad compression method" error when using PhpZipProxy.
						'DELIMITER_LEFT'  => '{',
						'DELIMITER_RIGHT' => '}'
					)
				);
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				dol_syslog($e->getMessage(), LOG_INFO);
				return -1;
			}

			// Define substitution array
			$substitutionarray            = getCommonSubstitutionArray($outputlangs, 0, null, $object);
			$array_object_from_properties = $this->get_substitutionarray_each_var_object($object, $outputlangs);
			$array_object                 = $this->get_substitutionarray_object($object, $outputlangs);
			$array_soc                    = $this->get_substitutionarray_mysoc($mysoc, $outputlangs);
			$array_soc['mycompany_logo']  = preg_replace('/_small/', '_mini', $array_soc['mycompany_logo']);

			$tmparray = array_merge($substitutionarray, $array_object_from_properties, $array_object, $array_soc);
			complete_substitutions_array($tmparray, $outputlangs, $object);

			$digiriskelement    = new DigiriskElement($this->db);
			$resources          = new DigiriskResources($this->db);
			$signatory          = new PreventionPlanSignature($this->db);
			$societe            = new Societe($this->db);
			$preventionplanline = new PreventionPlanLine($this->db);
			$risk               = new Risk($this->db);

			$preventionplanlines = $preventionplanline->fetchAll('', '', 0, 0, array(), 'AND', $preventionplan->id);

			$digirisk_resources     = $resources->digirisk_dolibarr_fetch_resources();
			$extsociety             = $resources->fetchResourcesFromObject('PP_EXT_SOCIETY', $preventionplan);
			if ($extsociety < 1) {
				$extsociety = new stdClass();
			}

			$maitreoeuvre           = $signatory->fetchSignatory('PP_MAITRE_OEUVRE', $preventionplan->id, 'preventionplan');
			$maitreoeuvre           = is_array($maitreoeuvre) ? array_shift($maitreoeuvre) : $maitreoeuvre;
			$extsocietyresponsible  = $signatory->fetchSignatory('PP_EXT_SOCIETY_RESPONSIBLE', $preventionplan->id, 'preventionplan');
			$extsocietyresponsible  = is_array($extsocietyresponsible) ? array_shift($extsocietyresponsible) : $extsocietyresponsible;
			$extsocietyintervenants = $signatory->fetchSignatory('PP_EXT_SOCIETY_INTERVENANTS', $preventionplan->id, 'preventionplan');

			$tmparray['titre_prevention']             = $preventionplan->ref;
			$tmparray['raison_du_plan_de_prevention'] = $preventionplan->label;

			$tmparray['prior_visit_date'] = dol_print_date($preventionplan->prior_visit_date, 'dayhoursec');
			$tmparray['prior_visit_text'] = $preventionplan->prior_visit_text;

			$tmparray['date_start_intervention_PPP'] = dol_print_date($preventionplan->date_start, 'dayhoursec');
			$tmparray['date_end_intervention_PPP']   = dol_print_date($preventionplan->date_end, 'dayhoursec');
			if (is_array($preventionplanlines)) {
				$tmparray['interventions_info'] = count($preventionplanlines) . " " . $langs->trans('PreventionPlanLine');
			} else {
				$tmparray['interventions_info'] = 0;
			}

			$openinghours = new Openinghours($this->db);

			$morewhere  = ' AND element_id = ' . $preventionplan->id;
			$morewhere .= ' AND element_type = ' . "'" . $preventionplan->element . "'";
			$morewhere .= ' AND status = 1';

			$openinghours->fetch(0, '', $morewhere);

			$opening_hours_monday    = explode(' ', $openinghours->monday);
			$opening_hours_tuesday   = explode(' ', $openinghours->tuesday);
			$opening_hours_wednesday = explode(' ', $openinghours->wednesday);
			$opening_hours_thursday  = explode(' ', $openinghours->thursday);
			$opening_hours_friday    = explode(' ', $openinghours->friday);
			$opening_hours_saturday  = explode(' ', $openinghours->saturday);
			$opening_hours_sunday    = explode(' ', $openinghours->sunday);

			$tmparray['lundi_matin']    = $opening_hours_monday[0];
			$tmparray['lundi_aprem']    = $opening_hours_monday[1];
			$tmparray['mardi_matin']    = $opening_hours_tuesday[0];
			$tmparray['mardi_aprem']    = $opening_hours_tuesday[1];
			$tmparray['mercredi_matin'] = $opening_hours_wednesday[0];
			$tmparray['mercredi_aprem'] = $opening_hours_wednesday[1];
			$tmparray['jeudi_matin']    = $opening_hours_thursday[0];
			$tmparray['jeudi_aprem']    = $opening_hours_thursday[1];
			$tmparray['vendredi_matin'] = $opening_hours_friday[0];
			$tmparray['vendredi_aprem'] = $opening_hours_friday[1];
			$tmparray['samedi_matin']   = $opening_hours_saturday[0];
			$tmparray['samedi_aprem']   = $opening_hours_saturday[1];
			$tmparray['dimanche_matin'] = $opening_hours_sunday[0];
			$tmparray['dimanche_aprem'] = $opening_hours_sunday[1];

			if ( ! empty($digirisk_resources)) {
				$societe->fetch($digirisk_resources['Pompiers']->id[0]);
				$tmparray['pompier_number'] = $societe->phone;

				$societe->fetch($digirisk_resources['SAMU']->id[0]);
				$tmparray['samu_number'] = $societe->phone;

				$societe->fetch($digirisk_resources['AllEmergencies']->id[0]);
				$tmparray['emergency_number'] = $societe->phone;

				$societe->fetch($digirisk_resources['Police']->id[0]);
				$tmparray['police_number'] = $societe->phone;
			}

			//Informations entreprise extérieure

			if ( ! empty($extsociety) && $extsociety > 0) {
				$tmparray['society_title']    = $extsociety->name;
				$tmparray['society_siret_id'] = $extsociety->idprof2;
				$tmparray['society_address']  = $extsociety->address;
				$tmparray['society_postcode'] = $extsociety->zip;
				$tmparray['society_town']     = $extsociety->town;
			}

			if ( ! empty($extsocietyintervenants) && $extsocietyintervenants > 0 && is_array($extsocietyintervenants)) {
				$tmparray['intervenants_info'] = count($extsocietyintervenants);
			} else {
				$tmparray['intervenants_info'] = 0;
			}

			$tempdir = $conf->digiriskdolibarr->multidir_output[isset($object->entity) ? $object->entity : 1] . '/temp/';

			//Signatures
			if ( ! empty($maitreoeuvre) && $maitreoeuvre > 0) {
				$tmparray['maitre_oeuvre_lname'] = $maitreoeuvre->lastname;
				$tmparray['maitre_oeuvre_fname'] = $maitreoeuvre->firstname;
				$tmparray['maitre_oeuvre_email'] = $maitreoeuvre->email;
				$tmparray['maitre_oeuvre_phone'] = $maitreoeuvre->phone;

				$tmparray['maitre_oeuvre_signature_date'] = dol_print_date($maitreoeuvre->signature_date, 'dayhoursec');
				if ((!preg_match('/specimen/i', $tempfilepath[1]) && $preventionplan->status >= $preventionplan::STATUS_LOCKED)) {
					$encoded_image = explode(",", $maitreoeuvre->signature)[1];
					$decoded_image = base64_decode($encoded_image);
					file_put_contents($tempdir . "signature.png", $decoded_image);
					$tmparray['maitre_oeuvre_signature'] = $tempdir . "signature.png";
				} else {
					$tmparray['maitre_oeuvre_signature'] = '';
				}
			}

			if ( ! empty($extsocietyresponsible) && $extsocietyresponsible > 0) {
				$tmparray['intervenant_exterieur_lname'] = $extsocietyresponsible->lastname;
				$tmparray['intervenant_exterieur_fname'] = $extsocietyresponsible->firstname;
				$tmparray['intervenant_exterieur_email'] = $extsocietyresponsible->email;
				$tmparray['intervenant_exterieur_phone'] = $extsocietyresponsible->phone;

				$tmparray['intervenant_exterieur_signature_date'] = dol_print_date($extsocietyresponsible->signature_date, 'dayhoursec');
				if ((!preg_match('/specimen/i', $tempfilepath[1]) && $preventionplan->status >= $preventionplan::STATUS_LOCKED)) {
					$encoded_image = explode(",", $extsocietyresponsible->signature)[1];
					$decoded_image = base64_decode($encoded_image);
					file_put_contents($tempdir . "signature2.png", $decoded_image);
					$tmparray['intervenant_exterieur_signature'] = $tempdir . "signature2.png";
				} else {
					$tmparray['intervenant_exterieur_signature'] = '';
				}
			}

			foreach ($tmparray as $key => $value) {
				try {
					if ($key == 'maitre_oeuvre_signature' || $key == 'intervenant_exterieur_signature') { // Image
						if (file_exists($value)) {
							$list     = getimagesize($value);
							$newWidth = 350;
							if ($list[0]) {
								$ratio     = $newWidth / $list[0];
								$newHeight = $ratio * $list[1];
								dol_imageResizeOrCrop($value, 0, $newWidth, $newHeight);
							}
							$odfHandler->setImage($key, $value);
						} else {
							$odfHandler->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
						}
					} elseif (preg_match('/logo$/', $key)) {
						if (file_exists($value)) $odfHandler->setImage($key, $value);
						else $odfHandler->setVars($key, $langs->transnoentities('ErrorFileNotFound'), true, 'UTF-8');
					} elseif (empty($value)) { // Text
						$odfHandler->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
					} else {
						$odfHandler->setVars($key, html_entity_decode($value, ENT_QUOTES | ENT_HTML5), true, 'UTF-8');
					}
				} catch (OdfException $e) {
					dol_syslog($e->getMessage(), LOG_INFO);
				}
			}
			// Replace tags of lines
			try {
				$foundtagforlines = 1;
				if ($foundtagforlines) {
					$listlines = $odfHandler->setSegment('interventions');
					if ( ! empty($preventionplanlines) && $preventionplanlines > 0) {
						foreach ($preventionplanlines as $line) {
							$digiriskelement->fetch($line->fk_element);

							$tmparray['key_unique']    = $line->ref;
							$tmparray['unite_travail'] = $digiriskelement->ref . " - " . $digiriskelement->label;
							$tmparray['action']        = $line->description;
							$tmparray['risk']          = DOL_DOCUMENT_ROOT . '/custom/digiriskdolibarr/img/categorieDangers/' . $risk->get_danger_category($line) . '.png';
							$tmparray['nomPicto']      = (!empty($conf->global->DIGIRISKDOLIBARR_DOCUMENT_SHOW_PICTO_NAME) ? $risk->get_danger_category_name($line) : ' ');
							$tmparray['prevention']    = $line->prevention_method;

							foreach ($tmparray as $key => $val) {
								try {
									if ($key == 'risk') {
										$listlines->setImage($key, $val);
									} elseif (empty($val)) {
										$listlines->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
									} else {
										$listlines->setVars($key, html_entity_decode($val, ENT_QUOTES | ENT_HTML5), true, 'UTF-8');
									}
								} catch (OdfException $e) {
									dol_syslog($e->getMessage(), LOG_INFO);
								} catch (SegmentException $e) {
									dol_syslog($e->getMessage(), LOG_INFO);
								}
							}
							$listlines->merge();
						}
						$odfHandler->mergeSegment($listlines);
					} else {
						$tmparray['key_unique']    = '';
						$tmparray['unite_travail'] = '';
						$tmparray['action']        = '';
						$tmparray['risk']          = '';
						$tmparray['nomPicto']      = '';
						$tmparray['prevention']    = '';

						foreach ($tmparray as $key => $val) {
							try {
								if (empty($val)) {
									$listlines->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
								} else {
									$listlines->setVars($key, html_entity_decode($val, ENT_QUOTES | ENT_HTML5), true, 'UTF-8');
								}
							} catch (SegmentException $e) {
								dol_syslog($e->getMessage(), LOG_INFO);
							}
						}
						$listlines->merge();
						$odfHandler->mergeSegment($listlines);
					}

					$listlines = $odfHandler->setSegment('intervenants');
					if ( ! empty($extsocietyintervenants) && $extsocietyintervenants > 0) {
						$k         = 3;
						foreach ($extsocietyintervenants as $line) {
							if ($line->status == 5) {
								if ((!preg_match('/specimen/i', $tempfilepath[1]) && $preventionplan->status >= $preventionplan::STATUS_LOCKED)) {
									$encoded_image = explode(",", $line->signature)[1];
									$decoded_image = base64_decode($encoded_image);
									file_put_contents($tempdir . "signature" . $k . ".png", $decoded_image);
									$tmparray['intervenants_signature'] = $tempdir . "signature" . $k . ".png";
								} else {
									$tmparray['intervenants_signature'] = '';
								}
							} else {
								$tmparray['intervenants_signature'] = '';
							}
							$tmparray['name']     = $line->firstname;
							$tmparray['lastname'] = $line->lastname;
							$tmparray['phone']    = $line->phone;
							$tmparray['mail']     = $line->email;
							$tmparray['status']   = $line->getLibStatut(1);

							$k++;

							foreach ($tmparray as $key => $value) {
								try {
									if ($key == 'intervenants_signature' && $line->status == 5) { // Image
										if (file_exists($value)) {
											$list     = getimagesize($value);
											$newWidth = 200;
											if ($list[0]) {
												$ratio     = $newWidth / $list[0];
												$newHeight = $ratio * $list[1];
												dol_imageResizeOrCrop($value, 0, $newWidth, $newHeight);
											}
											$listlines->setImage($key, $value);
										} else {
											$odfHandler->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
										}
									} elseif (empty($value)) {  // Text
										$listlines->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
									} else {
										$listlines->setVars($key, html_entity_decode($value, ENT_QUOTES | ENT_HTML5), true, 'UTF-8');
									}
								} catch (OdfException $e) {
									dol_syslog($e->getMessage(), LOG_INFO);
								} catch (SegmentException $e) {
									dol_syslog($e->getMessage(), LOG_INFO);
								}
							}
							$listlines->merge();

							if ((!preg_match('/specimen/i', $tempfilepath[1]) && $preventionplan->status >= $preventionplan::STATUS_LOCKED)) {
								dol_delete_file($tempdir . "signature" . $k . ".png");
							}
						}
						$odfHandler->mergeSegment($listlines);
					} else {
						$tmparray['intervenants_signature'] = '';
						$tmparray['name']                   = '';
						$tmparray['lastname']               = '';
						$tmparray['phone']                  = '';
						$tmparray['mail']                   = '';
						$tmparray['status']                 = '';

						foreach ($tmparray as $key => $val) {
							try {
								if (empty($val)) {
									$listlines->setVars($key, $langs->trans('NoData'), true, 'UTF-8');
								} else {
									$listlines->setVars($key, html_entity_decode($val, ENT_QUOTES | ENT_HTML5), true, 'UTF-8');
								}
							} catch (SegmentException $e) {
								dol_syslog($e->getMessage(), LOG_INFO);
							}
						}
						$listlines->merge();
						$odfHandler->mergeSegment($listlines);
					}
				}
			} catch (OdfException $e) {
				$this->error = $e->getMessage();
				dol_syslog($this->error, LOG_WARNING);
				return -1;
			}

			// Replace labels translated
			$tmparray = $outputlangs->get_translations_for_substitutions();
			foreach ($tmparray as $key => $value) {
				try {
					$odfHandler->setVars($key, $value, true, 'UTF-8');
				} catch (OdfException $e) {
					dol_syslog($e->getMessage(), LOG_INFO);
				}
			}

			// Call the beforeODTSave hook
			$parameters = array('odfHandler' => &$odfHandler, 'file' => $file, 'object' => $object, 'outputlangs' => $outputlangs, 'substitutionarray' => &$tmparray);
			$hookmanager->executeHooks('beforeODTSave', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks

			// Write new file
			if ( ! empty($conf->global->MAIN_ODT_AS_PDF)) {
				try {
					$odfHandler->exportAsAttachedPDF($file);
				} catch (Exception $e) {
					$this->error = $e->getMessage();
					dol_syslog($e->getMessage(), LOG_INFO);
					return -1;
				}
			} else {
				try {
					$odfHandler->saveToDisk($file);
				} catch (Exception $e) {
					$this->error = $e->getMessage();
					dol_syslog($e->getMessage(), LOG_INFO);
					return -1;
				}
			}

			$parameters = array('odfHandler' => &$odfHandler, 'file' => $file, 'object' => $object, 'outputlangs' => $outputlangs, 'substitutionarray' => &$tmparray);
			$hookmanager->executeHooks('afterODTCreation', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks

//			if ( ! empty($conf->global->MAIN_UMASK))
//				@chmod($file, octdec($conf->global->MAIN_UMASK));

			$odfHandler = null; // Destroy object

			if ((!preg_match('/specimen/i', $tempfilepath[1]) && $preventionplan->status >= $preventionplan::STATUS_LOCKED)) {
				dol_delete_file($tempdir . "signature.png");
				dol_delete_file($tempdir . "signature2.png");
			}

			$this->result = array('fullpath' => $file);

			return 1; // Success
		} else {
			$this->error = $langs->transnoentities("ErrorCanNotCreateDir", $dir);
			return -1;
		}
	}
}
