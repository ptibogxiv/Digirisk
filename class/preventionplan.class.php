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
 */

/**
 * \file        class/preventionplandocument.class.php
 * \ingroup     digiriskdolibarr
 * \brief       This file is a class file for PreventionPlan
 */

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

require_once __DIR__ . '/digiriskdocuments.class.php';
require_once __DIR__ . '/digirisksignature.class.php';

/**
 * Class for PreventionPlan
 */
class PreventionPlan extends CommonObject
{

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $element = 'preventionplan';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'digiriskdolibarr_preventionplan';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 1;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for digiriskelement. Must be the part after the 'object_' into object_digiriskelement.png
	 */
	public $picto = 'preventionplandocument@digiriskdolibarr';

	const STATUS_IN_PROGRESS       = 1;
	const STATUS_PENDING_SIGNATURE = 2;
	const STATUS_LOCKED            = 3;
	const STATUS_UNLOCKED          = 4;
	const STATUS_ARCHIVED          = 5;

	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid'              => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'comment'=>"Id"),
		'ref'                => array('type'=>'varchar(128)', 'label'=>'Ref', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>1, 'noteditable'=>'1', 'default'=>'(PROV)', 'index'=>1, 'searchall'=>1, 'showoncombobox'=>'1', 'comment'=>"Reference of object"),
		'ref_ext'            => array('type'=>'varchar(128)', 'label'=>'RefExt', 'enabled'=>'1', 'position'=>20, 'notnull'=>0, 'visible'=>0,),
		'entity'             => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>'1', 'position'=>30, 'notnull'=>1, 'visible'=>-1,),
		'date_creation'      => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>40, 'notnull'=>1, 'visible'=>-2,),
		'tms'                => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>50, 'notnull'=>0, 'visible'=>-2,),
		'status'             => array('type'=>'smallint', 'label'=>'Status', 'enabled'=>'1', 'position'=>70, 'notnull'=>0, 'visible'=>1, 'index'=>0,),
		'label'              => array('type'=>'varchar(255)', 'label'=>'Label', 'enabled'=>'1', 'position'=>80, 'notnull'=>0, 'visible'=>1, 'searchall'=>1, 'css'=>'minwidth200', 'help'=>"Help text", 'showoncombobox'=>'1',),
		'date_start'         => array('type'=>'datetime', 'label'=>'StartDate', 'enabled'=>'1', 'position'=>100, 'notnull'=>-1, 'visible'=>1,),
		'date_end'           => array('type'=>'datetime', 'label'=>'EndDate', 'enabled'=>'1', 'position'=>130, 'notnull'=>-1, 'visible'=>1,),
		'prior_visit_bool'   => array('type'=>'boolean', 'label'=>'PriorVisitYesNo', 'enabled'=>'1', 'position'=>140, 'notnull'=>-1, 'visible'=>1,),
		'prior_visit_text'   => array('type'=>'text', 'label'=>'PriorVisitText', 'enabled'=>'1', 'position'=>150, 'notnull'=>-1, 'visible'=>1,),
		'cssct_intervention' => array('type'=>'boolean', 'label'=>'CSSCTIntervention', 'enabled'=>'1', 'position'=>160, 'notnull'=>-1, 'visible'=>1,),
		'fk_project'         => array('type'=>'integer:Project:projet/class/project.class.php', 'label'=>'Projet', 'enabled'=>'1', 'position'=>170, 'notnull'=>1, 'visible'=>1,),
		'fk_user_creat'      => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthor', 'enabled'=>'1', 'position'=>180, 'notnull'=>1, 'visible'=>-2, 'foreignkey'=>'user.rowid',),
		'fk_user_modif'      => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModif', 'enabled'=>'1', 'position'=>190, 'notnull'=>-1, 'visible'=>-2,),
	);

	public $rowid;
	public $ref;
	public $ref_ext;
	public $entity;
	public $date_creation;
	public $tms;
	public $status;
	public $label;
	public $date_start;
	public $date_end;
	public $prior_visit_bool;
	public $prior_visit_text;
	public $cssct_intervention;
	public $fk_project;
	public $fk_user_creat;
	public $fk_user_modif;

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs))
		{
			foreach ($this->fields as $key => $val)
			{
				if (is_array($val['arrayofkeyval']))
				{
					foreach ($val['arrayofkeyval'] as $key2 => $val2)
					{
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}
	}


	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		$this->element = $this->element . '@digiriskdolibarr';
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $conf, $langs, $extrafields;
		$error = 0;

		$signatory         = new PreventionPlanSignature($this->db);
		$digiriskresources = new DigiriskResources($this->db);

		$refPreventionPlanMod = new $conf->global->DIGIRISKDOLIBARR_PREVENTIONPLAN_ADDON($this->db);

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) {
			$object->fetchLines();
		}

		// Load signatory and ressources form source object
		$signatories      = $signatory->fetchSignatory("", $fromid);
		$object_resources = $digiriskresources->fetchResourcesFromObject('', $object);

		if (!empty ($signatories) && $signatories > 0) {
			foreach ($signatories as $arrayRole) {
				foreach ($arrayRole as $signatory) {
					$signatoriesID[$signatory->role] = $signatory->element_id;
				}
			}
		}

		if (!empty ($object_resources) && $object_resources > 0) {
			foreach ($object_resources as $arrayRole) {
				foreach ($arrayRole as $object_resource) {
					$ressources[] = $object_resource->id;
				}
			}
		}

		$arrayRole = array( 'PP_EXT_SOCIETY', 'PP_LABOUR_INSPECTOR', 'PP_LABOUR_INSPECTOR_ASSIGNED');
		$ArrayRessources = array_flip($arrayRole);
		$ArrayRessources['PP_EXT_SOCIETY'] = $ressources[0];
		$ArrayRessources['PP_LABOUR_INSPECTOR'] = $ressources[1];
		$ArrayRessources['PP_LABOUR_INSPECTOR_ASSIGNED'] = $ressources[2];

		// get lines so they will be clone
		//foreach($this->lines as $line)
		//	$line->fetch_optionals();

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);

		// Clear fields
		if (property_exists($object, 'ref')) {
			$object->ref = $refPreventionPlanMod->getNextValue($object);
		}
		if (property_exists($object, 'ref_ext')) {
			$object->ref_ext = 'digirisk_' . $object->ref;
		}
		if (property_exists($object, 'label')) {
			$object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		}
		if (property_exists($object, 'status')) {
			$object->status = self::STATUS_IN_PROGRESS;
		}
		if (property_exists($object, 'date_creation')) {
			$object->date_creation = dol_now();
		}

		// ...
//		// Clear extrafields that are unique
////		if (is_array($object->array_options) && count($object->array_options) > 0) {
////			$extrafields->fetch_name_optionals_label($this->table_element);
////			foreach ($object->array_options as $key => $option) {
////				$shortkey = preg_replace('/options_/', '', $key);
////				if (!empty($extrafields->attributes[$this->table_element]['unique'][$shortkey])) {
////					//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
////					unset($object->array_options[$key]);
////				}
////			}
////		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);

		if ($result > 0) {
			$digiriskresources->digirisk_dolibarr_set_resources($this->db, $user->id, 'PP_EXT_SOCIETY', 'societe', array($ArrayRessources['PP_EXT_SOCIETY']), $conf->entity, 'preventionplan', $result, 0);
			$digiriskresources->digirisk_dolibarr_set_resources($this->db, $user->id, 'PP_LABOUR_INSPECTOR', 'societe', array($ArrayRessources['PP_LABOUR_INSPECTOR']), $conf->entity, 'preventionplan', $result, 0);
			$digiriskresources->digirisk_dolibarr_set_resources($this->db, $user->id, 'PP_LABOUR_INSPECTOR_ASSIGNED', 'socpeople', array($ArrayRessources['PP_LABOUR_INSPECTOR_ASSIGNED']), $conf->entity, 'preventionplan', $result, 0);
			$signatory->setSignatory($result, 'user', array($signatoriesID['PP_MAITRE_OEUVRE']), 'PP_MAITRE_OEUVRE');
			$signatory->setSignatory($result, 'socpeople', array($signatoriesID['PP_EXT_SOCIETY_RESPONSIBLE']), 'PP_EXT_SOCIETY_RESPONSIBLE');
		} else {
			$error++;
			$this->error = $object->error;
			$this->errors = $object->errors;
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $result;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		return $this->fetchCommon($id, $ref);
	}

	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
		else $sql .= ' WHERE 1 = 1';
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 't.rowid') {
					$sqlwhere[] = $key.'='.$value;
				}
				elseif (strpos($key, 'date') !== false) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				}
				elseif ($key == 'customsql') {
					$sqlwhere[] = $value;
				}
				else {
					$sqlwhere[] = $key.' LIKE \'%'.$this->db->escape($value).'%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ('.implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= ' '.$this->db->plimit($limit, $offset);
		}
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}


	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql .= ' fk_user_creat, fk_user_modif';
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql .= ' WHERE t.rowid = '.$id;
		$result = $this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 *	Set in progress status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setInProgress($user, $notrigger = 0)
	{
		$this->deleteSignatoriesSignatures();
		return $this->setStatusCommon($user, self::STATUS_IN_PROGRESS, $notrigger, 'PREVENTIONPLAN_INPROGRESS');
	}
	/**
	 *	Set pending signature status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setPendingSignature($user, $notrigger = 0)
	{
		return $this->setStatusCommon($user, self::STATUS_PENDING_SIGNATURE, $notrigger, 'PREVENTIONPLAN_PENDINGSIGNATURE');
	}

	/**
	 *	Set lock status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setLocked($user, $notrigger = 0)
	{
		return $this->setStatusCommon($user, self::STATUS_LOCKED, $notrigger, 'PREVENTIONPLAN_LOCKED');
	}

	/**
	 *	Set unlock status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setUnlocked($user, $notrigger = 0)
	{
		return $this->setStatusCommon($user, self::STATUS_UNLOCKED, $notrigger, 'PREVENTIONPLAN_UNLOCKED');
	}

	/**
	 *	Set close status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setArchived($user, $notrigger = 0)
	{
		return $this->setStatusCommon($user, self::STATUS_ARCHIVED, $notrigger, 'PREVENTIONPLAN_ARCHIVED');
	}

	/**
	 *  Return the label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode = 0)
	{

		// phpcs:enable
		if (empty($this->labelStatus) || empty($this->labelStatusShort))
		{
			global $langs;
			$langs->load("digiriskdolibarr@digiriskdolibarr");

			$this->labelStatus[self::STATUS_IN_PROGRESS] = $langs->trans('InProgress');
			$this->labelStatus[self::STATUS_PENDING_SIGNATURE] = $langs->trans('ValidatePendingSignature');
			$this->labelStatus[self::STATUS_LOCKED] = $langs->trans('Locked');
			$this->labelStatus[self::STATUS_UNLOCKED] = $langs->trans('Unlocked');
			$this->labelStatus[self::STATUS_ARCHIVED] = $langs->trans('Archived');
		}

		$statusType = 'status'.$status;
		if ($status == self::STATUS_PENDING_SIGNATURE) $statusType = 'status3';
		if ($status == self::STATUS_UNLOCKED) $statusType = 'status4';
		if ($status == self::STATUS_LOCKED) $statusType = 'status8';
		if ($status == self::STATUS_ARCHIVED) $statusType = 'status8';

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *    	Return a link on thirdparty (with picto)
	 *
	 *		@param	int		$withpicto		          Add picto into link (0=No picto, 1=Include picto with link, 2=Picto only)
	 *		@param	string	$option			          Target of link ('', 'customer', 'prospect', 'supplier', 'project')
	 *		@param	int		$maxlen			          Max length of name
	 *      @param	int  	$notooltip		          1=Disable tooltip
	 *      @param  int     $save_lastsearch_value    -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *		@return	string					          String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $maxlen = 0, $notooltip = 0, $save_lastsearch_value = -1)
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) $notooltip = 1; // Force disable tooltips

		$name = $this->ref;



		$result = ''; $label = '';
		$linkstart = ''; $linkend = '';

		if (!empty($this->logo) && class_exists('Form'))
		{
			$label .= '<div class="photointooltip">';
			$label .= Form::showphoto('societe', $this, 0, 40, 0, '', 'mini', 0); // Important, we must force height so image will have height tags and if image is inside a tooltip, the tooltip manager can calculate height and position correctly the tooltip.
			$label .= '</div><div style="clear: both;"></div>';
		}
		elseif (!empty($this->logo_squarred) && class_exists('Form'))
		{
			/*$label.= '<div class="photointooltip">';
			$label.= Form::showphoto('societe', $this, 0, 40, 0, 'photowithmargin', 'mini', 0);	// Important, we must force height so image will have height tags and if image is inside a tooltip, the tooltip manager can calculate height and position correctly the tooltip.
			$label.= '</div><div style="clear: both;"></div>';*/
		}

		$label .= '<div class="centpercent">';


		// By default
		if (empty($linkstart))
		{
			$label .= '<u>'.$langs->trans("PreventionPlan").'</u>';
			$linkstart = '<a href="'.DOL_URL_ROOT.'/custom/digiriskdolibarr/preventionplan_card.php?id='.$this->id;
		}

		if (!empty($this->ref))
		{
			$label .= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
		}

		$label .= '</div>';

		$linkstart .= '"';

		$linkclose = '';
		if (empty($notooltip))
		{
			if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label = $langs->trans("ShowCompany");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose .= ' class="classfortooltip refurl"';
		}
		$linkstart .= $linkclose.'>';
		$linkend = '</a>';

		$result .= $linkstart;
		if ($withpicto) $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
		if ($withpicto != 2) $result .= ($maxlen ?dol_trunc($name, $maxlen) : $name);
		$result .= $linkend;

		 $result .= $hookmanager->resPrint;

		return $result;
	}
	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Output html form to select a third party.
	 *  Note, you must use the select_company to get the component to select a third party. This function must only be called by select_company.
	 *
	 *	@param	string	$selected       Preselected type
	 *	@param  string	$htmlname       Name of field in form
	 *  @param  string	$filter         Optional filters criteras (example: 's.rowid <> x', 's.client in (1,3)')
	 *	@param	string	$showempty		Add an empty field (Can be '1' or text to use on empty line like 'SelectThirdParty')
	 * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
	 * 	@param	int		$forcecombo		Force to use standard HTML select component without beautification
	 *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *  @param	string	$filterkey		Filter on key value
	 *  @param	int		$outputmode		0=HTML select string, 1=Array
	 *  @param	int		$limit			Limit number of answers
	 *  @param	string	$morecss		Add more css styles to the SELECT component
	 *	@param  string	$moreparam      Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
	 *	@param  bool	$multiple       add [] in the name of element and add 'multiple' attribut
	 * 	@return	string					HTML string with
	 */
	public function select_preventionplan_list($selected = '', $htmlname = 'fk_preventionplan', $filter = '', $showempty = '1', $showtype = 0, $forcecombo = 0, $events = array(), $filterkey = '', $outputmode = 0, $limit = 0, $morecss = 'minwidth100', $moreparam = '', $multiple = false)
	{
		// phpcs:enable
		global $conf, $user, $langs;

		$out = '';
		$num = 0;
		$outarray = array();

		if ($selected === '') $selected = array();
		elseif (!is_array($selected)) $selected = array($selected);

		// Clean $filter that may contains sql conditions so sql code
		if (function_exists('testSqlAndScriptInject')) {
			if (testSqlAndScriptInject($filter, 3) > 0) {
				$filter = '';
			}
		}
		// On recherche les societes
		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= " FROM ".MAIN_DB_PREFIX."digiriskdolibarr_preventionplan as s";

		$sql .= " WHERE s.entity IN (".getEntity($this->table_element).")";
		if ($filter) $sql .= " AND (".$filter.")";
		$sql .= " AND status = 1";
		$sql .= $this->db->order("rowid", "ASC");
		$sql .= $this->db->plimit($limit, 0);

		// Build output string
		dol_syslog(get_class($this)."::select_preventionplan_list", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			if (!$forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlname, $events, 0);
			}

			// Construct $out and $outarray
			$out .= '<select id="'.$htmlname.'" class="flat'.($morecss ? ' '.$morecss : '').'"'.($moreparam ? ' '.$moreparam : '').' name="'.$htmlname.($multiple ? '[]' : '').'" '.($multiple ? 'multiple' : '').'>'."\n";

			$textifempty = (($showempty && !is_numeric($showempty)) ? $langs->trans($showempty) : '');
			if ($showempty) $out .= '<option value="-1">'.$textifempty.'</option>'."\n";

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label = $obj->ref;


					if (empty($outputmode))
					{
						if (in_array($obj->rowid, $selected))
						{
							$out .= '<option value="'.$obj->rowid.'" selected>'.$label.'</option>';
						}
						else
						{
							$out .= '<option value="'.$obj->rowid.'">'.$label.'</option>';
						}
					}
					else
					{
						array_push($outarray, array('key'=>$obj->rowid, 'value'=>$label, 'label'=>$label));
					}

					$i++;
					if (($i % 10) == 0) $out .= "\n";
				}
			}
			$out .= '</select>'."\n";
		}
		else
		{
			dol_print_error($this->db);
		}

		$this->result = array('nbofpreventionplan'=>$num);

		if ($outputmode) return $outarray;
		return $out;
	}

	public function fetchSignatories($morefilter = '1 = 1') {
		$signatoryObj = new PreventionPlanSignature($this->db);
		$signatories = $signatoryObj->fetchAll('','', 0, 0, array('fk_object' => $this->id, 'customsql' => $morefilter),'AND');
		return $signatories;
	}

	public function checkSignatoriesSignatures() {
		$morefilter = 'status != 0';
		$signatories = $this->fetchSignatories($morefilter);

		if (!empty($signatories) && $signatories > 0) {
			foreach ($signatories as $signatory) {
				if (dol_strlen($signatory->signature)) {
					continue;
				} else {
					return 0;
				}
			}
			return 1;
		}
	}

	public function deleteSignatoriesSignatures() {
		global $user;

		$signatories = $this->fetchSignatories();

		if (!empty($signatories) && $signatories > 0) {
			foreach ($signatories as $signatory) {
				if (dol_strlen($signatory->signature)) {
					$signatory->signature = '';
					$signatory->signature_date = '';
					$signatory->status = 1;
					$signatory->update($user);
				}
			}
			return 1;
		}
	}
}
/**
 *	Class to manage invoice lines.
 *  Saved into database table llx_preventionplandet
 */
class PreventionPlanLine extends CommonObjectLine
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'preventionplandet';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'preventionplandet';

	public $ref = '';

	public $date_creation = '';

	public $description = '';

	public $category = '';

	public $prevention_method = '';

	public $fk_preventionplan = '';

	public $fk_element = '';


	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

	}

	/**
	 *	Load prevention plan line from database
	 *
	 *	@param	int		$rowid      id of invoice line to get
	 *	@return	int					<0 if KO, >0 if OK
	 */
	public function fetch($rowid)
	{
		global $db;

		$sql = 'SELECT  t.rowid, t.ref, t.date_creation, t.description, t.category, t.prevention_method, t.fk_preventionplan, t.fk_element ';
		$sql .= ' FROM '.MAIN_DB_PREFIX.'digiriskdolibarr_preventionplandet as t';
		$sql .= ' WHERE t.rowid = '.$rowid;
		$sql .= ' AND entity IN ('.getEntity($this->table_element).')';

		$result = $db->query($sql);
		if ($result)
		{
			$objp = $db->fetch_object($result);

			$this->id                = $objp->rowid;
			$this->ref               = $objp->ref;
			$this->date_creation     = $objp->date_creation;
			$this->description       = $objp->description;
			$this->category          = $objp->category;
			$this->prevention_method = $objp->prevention_method;
			$this->fk_preventionplan = $objp->fk_preventionplan;
			$this->fk_element        = $objp->fk_element;

			$db->free($result);

			return 1;
		}
		else
		{
			$this->error = $db->lasterror();
			return -1;
		}
	}

	/**
	 *	Load preventionplan line line from database
	 *
	 *	@param	int		$rowid      id of preventionplan line line to get
	 *	@return	int					<0 if KO, >0 if OK
	 */
	public function fetchAll($parent_id = 0, $limit = 0)
	{
		global $db;
		$sql = 'SELECT  t.rowid, t.ref, t.date_creation, t.description, t.category, t.prevention_method, t.fk_element';
		$sql .= ' FROM '.MAIN_DB_PREFIX.'digiriskdolibarr_preventionplandet as t';
		if ($parent_id > 0) {
			$sql .= ' WHERE t.fk_preventionplan = '.$parent_id;
		} else {
			$sql .= ' WHERE 1=1';
		}
		$sql .= ' AND entity IN ('.getEntity($this->table_element).')';


		$result = $db->query($sql);

		if ($result)
		{
			$num = $db->num_rows($result);

			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $db->fetch_object($result);

				$record = new self($db);

				$record->id                = $obj->rowid;
				$record->ref               = $obj->ref;
				$record->date_creation     = $obj->date_creation;
				$record->description       = $obj->description;
				$record->category          = $obj->category;
				$record->prevention_method = $obj->prevention_method;
				$record->fk_preventionplan = $obj->fk_preventionplan;
				$record->fk_element        = $obj->fk_element;

				$records[$record->id] = $record;

				$i++;
			}

			$db->free($result);

			return $records;
		}
		else
		{
			$this->error = $db->lasterror();
			return -1;
		}
	}

	/**
	 *	Insert line into database
	 *
	 *	@param      int		$notrigger		                 1 no triggers
	 *  @param      int     $noerrorifdiscountalreadylinked  1=Do not make error if lines is linked to a discount and discount already linked to another
	 *	@return		int						                 <0 if KO, >0 if OK
	 */
	public function insert($notrigger = 0, $noerrorifdiscountalreadylinked = 0)
	{
		global $db, $user;

		$error = 0;


		// Clean parameters
		$this->description = trim($this->description);

		$db->begin();
		$now = dol_now();

		// Insertion dans base de la ligne
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'digiriskdolibarr_preventionplandet';
		$sql .= ' (ref, entity, date_creation, description, category, prevention_method, fk_preventionplan, fk_element';
		$sql .= ')';
		$sql .= " VALUES (";
		$sql .= "'" . $db->escape($this->ref) . "'" . ", ";
		$sql .= $this->entity . ", ";
		$sql .= "'" . $db->escape($db->idate($now)) . "'" . ", ";
		$sql .= "'" . $db->escape($this->description) . "'" . ", ";
		$sql .= $this->category . ", ";
		$sql .= "'" . $db->escape($this->prevention_method) . "'" . ", ";
		$sql .= $this->fk_preventionplan . ", ";
		$sql .= $this->fk_element ;

		$sql .= ')';

		dol_syslog(get_class($this)."::insert", LOG_DEBUG);
		$resql = $db->query($sql);

		if ($resql)
		{
			$this->id = $db->last_insert_id(MAIN_DB_PREFIX.'preventionplandet');
			$this->rowid = $this->id; // For backward compatibility

			$db->commit();
			return $this->id;
		}
		else
		{
			$this->error = $db->lasterror();
			$db->rollback();
			return -2;
		}
	}

	/**
	 *	Update line into database
	 *
	 *	@param		User	$user		User object
	 *	@param		int		$notrigger	Disable triggers
	 *	@return		int					<0 if KO, >0 if OK
	 */
	public function update($user = '', $notrigger = 1)
	{
		global $user, $conf, $db;

		$error = 0;

		// Clean parameters
		$this->description = trim($this->description);

		$db->begin();

		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."digiriskdolibarr_preventionplandet SET";
		$sql .= " ref='".$db->escape($this->ref)."',";
		$sql .= " description='".$db->escape($this->description)."',";
		$sql .= " category=".$db->escape($this->category) . ",";
		$sql .= " prevention_method='".$db->escape($this->prevention_method)."'" . ",";
		$sql .= " fk_preventionplan=".$db->escape($this->fk_preventionplan) . ",";
		$sql .= " fk_element=".$db->escape($this->fk_element);

		$sql .= " WHERE rowid = ".$this->id;

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $db->query($sql);
		if ($resql)
		{
			$db->commit();
			return 1;
		}
		else
		{
			$this->error = $db->error();
			$db->rollback();
			return -2;
		}
	}

	/**
	 * 	Delete line in database
	 *
	 *	@return	    int		           <0 if KO, >0 if OK
	 */
	public function delete()
	{
		global $user, $db;

		$db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."digiriskdolibarr_preventionplandet WHERE rowid = ".$this->id;
		dol_syslog(get_class($this)."::delete", LOG_DEBUG);
		if ($db->query($sql))
		{
			$db->commit();
			return 1;
		}
		else
		{
			$this->error = $db->error()." sql=".$sql;
			$db->rollback();
			return -1;
		}
	}

}

class PreventionPlanSignature extends DigiriskSignature
{
	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'digiriskdolibarr_preventionplan_signature';

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs))
		{
			foreach ($this->fields as $key => $val)
			{
				if (is_array($val['arrayofkeyval']))
				{
					foreach ($val['arrayofkeyval'] as $key2 => $val2)
					{
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}
	}

	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();
		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element;
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql .= ' WHERE entity IN ('.getEntity($this->table_element).')';
		else $sql .= ' WHERE 1 = 1';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 'rowid') {
					$sqlwhere[] = $key.'='.$value;
				} elseif (in_array($this->fields[$key]['type'], array('date', 'datetime', 'timestamp'))) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				} elseif ($key == 'customsql') {
					$sqlwhere[] = $value;
				} elseif (strpos($value, '%') === false) {
					$sqlwhere[] = $key.' IN ('.$this->db->sanitize($this->db->escape($value)).')';
				} else {
					$sqlwhere[] = $key.' LIKE \'%'.$this->db->escape($value).'%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ('.implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= ' '.$this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}
}
