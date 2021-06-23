<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * \file        class/digirisksignature.class.php
 * \ingroup     digiriskdolibarr
 * \brief       This file is a CRUD class file for DigiriskSignature (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for DigiriskSignature
 */
class DigiriskSignature extends CommonObject
{
	/**
	 * @var string ID of module.
	 */
	public $module = 'digiriskdolibarr';

	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'digirisksignature';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'digiriskdolibarr_digirisksignature';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 1;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;

	/**
	 * @var string String with name of icon for digirisksignature. Must be the part after the 'object_' into object_digirisksignature.png
	 */
	public $picto = 'digirisksignature@digiriskdolibarr';

	const STATUS_REGISTERED = 0;
	const STATUS_SIGNATURE_REQUEST = 1;
	const STATUS_PENDING_SIGNATURE = 2;
	const STATUS_DENIED = 3;
	const STATUS_UNSIGNED = 4;
	const STATUS_ABSENT = 5;
	const STATUS_JUSTIFIED_ABSENT = 6;

	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid'             => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'comment'=>"Id"),
		'entity'            => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>-1,),
		'date_creation'     => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>20, 'notnull'=>1, 'visible'=>-2,),
		'tms'               => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>30, 'notnull'=>0, 'visible'=>-2,),
		'import_key'        => array('type'=>'integer', 'label'=>'ImportId', 'enabled'=>'1', 'position'=>40, 'notnull'=>1, 'visible'=>-2,),
		'status'            => array('type'=>'smallint', 'label'=>'Status', 'enabled'=>'1', 'position'=>50, 'notnull'=>0, 'visible'=>1, 'index'=>1,),
		'role'              => array('type'=>'varchar(255)', 'label'=>'Role', 'enabled'=>'1', 'position'=>60, 'notnull'=>0, 'visible'=>3,),
		'firstname'         => array('type'=>'varchar(255)', 'label'=>'Firstname', 'enabled'=>'1', 'position'=>70, 'notnull'=>0, 'visible'=>3,),
		'lastname'          => array('type'=>'varchar(255)', 'label'=>'Lastname', 'enabled'=>'1', 'position'=>80, 'notnull'=>0, 'visible'=>3,),
		'email'             => array('type'=>'varchar(255)', 'label'=>'Email', 'enabled'=>'1', 'position'=>90, 'notnull'=>0, 'visible'=>3,),
		'phone'             => array('type'=>'varchar(255)', 'label'=>'Phone', 'enabled'=>'1', 'position'=>100, 'notnull'=>0, 'visible'=>3,),
		'society_name'      => array('type'=>'varchar(255)', 'label'=>'SocietyName', 'enabled'=>'1', 'position'=>110, 'notnull'=>0, 'visible'=>3,),
		'signature_date'    => array('type'=>'varchar(255)', 'label'=>'SignatureDate', 'enabled'=>'1', 'position'=>120, 'notnull'=>0, 'visible'=>3,),
		'signature_comment' => array('type'=>'varchar(255)', 'label'=>'SignatureComment', 'enabled'=>'1', 'position'=>130, 'notnull'=>0, 'visible'=>3,),
		'element_id'        => array('type'=>'integer', 'label'=>'ElementType', 'enabled'=>'1', 'position'=>140, 'notnull'=>1, 'visible'=>1,),
		'element_type'      => array('type'=>'varchar(50)', 'label'=>'ElementType', 'enabled'=>'1', 'position'=>150, 'notnull'=>0, 'visible'=>1,),
		'signature'         => array('type'=>'varchar(255)', 'label'=>'Signature', 'enabled'=>'1', 'position'=>160, 'notnull'=>0, 'visible'=>3,),
		'signature_url'     => array('type'=>'varchar(50)', 'label'=>'SignatureUrl', 'enabled'=>'1', 'position'=>170, 'notnull'=>0, 'visible'=>1, 'default'=>NULL,),
		'transaction_url'   => array('type'=>'varchar(50)', 'label'=>'TransactionUrl', 'enabled'=>'1', 'position'=>180, 'notnull'=>0, 'visible'=>1,'default'=>NULL,),
		'fk_object'         => array('type'=>'integer', 'label'=>'FKObject', 'enabled'=>'1', 'position'=>190, 'notnull'=>1, 'visible'=>0,),
	);

	public $rowid;
	public $entity;
	public $date_creation;
	public $tms;
	public $import_key;
	public $status;
	public $role;
	public $firstname;
	public $lastname;
	public $email;
	public $phone;
	public $society_name;
	public $signature_date;
	public $signature_comment;
	public $element_id;
	public $element_type;
	public $signature;
	public $signature_url;
	public $transaction_url;
	public $fk_object;

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

		// Example to show how to set values of fields definition dynamically
		/*if ($user->rights->digiriskdolibarr->digirisksignature->read) {
			$this->fields['myfield']['visible'] = 1;
			$this->fields['myfield']['noteditable'] = 0;
		}*/

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
				if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval']))
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
		return $this->createCommon($user, $notrigger);
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
		global $conf;

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
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 *	Set draft status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setDraft($user, $notrigger = 0)
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->digiriskdolibarr_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'DIGIRISKSIGNATURE_UNVALIDATE');
	}

	/**
	 *	Set cancel status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function cancel($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_VALIDATED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->digiriskdolibarr_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_CANCELED, $notrigger, 'DIGIRISKSIGNATURE_CANCEL');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_CANCELED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->digiriskdolibarr->digiriskdolibarr_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_VALIDATED, $notrigger, 'DIGIRISKSIGNATURE_REOPEN');
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
			//$langs->load("digiriskdolibarr@digiriskdolibarr");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
		}

		$statusType = 'status'.$status;
		//if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
		if ($status == self::STATUS_CANCELED) $statusType = 'status6';

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 * Clone an object into another one
	 *
	 * @param varchar $ref name of resource
	 * @param varchar $element_type type of resource
	 * @param int $element_id Id of resource
	 */
	function setSignatory($fk_object, $element_type, $element_ids, $role = "")
	{
		global $conf, $user;

		$society = new Societe($this->db);

		foreach ( $element_ids as $element_id ) {
			if ($element_type == 'user') {
				$signatory_data = new User($this->db);

				$signatory_data->fetch($element_id);

				if ($signatory_data->socid > 0) {
					$society->fetch($signatory_data->socid);
					$this->society_name = $society->name;
				} else {
					$this->society_name = $conf->global->MAIN_INFO_SOCIETE_NOM;
				}

				$this->phone = $signatory_data->user_mobile;

			} elseif ($element_type == 'socpeople') {
				$signatory_data = new Contact($this->db);

				$signatory_data->fetch($element_id);

				$society->fetch($signatory_data->fk_soc);

				$this->society_name = $society->name;
				$this->phone = $signatory_data->phone_pro;
			}

			$this->firstname = $signatory_data->firstname;
			$this->lastname = $signatory_data->lastname;
			$this->email = $signatory_data->email;
			$this->role = $role;

			$this->element_type = $element_type;
			$this->element_id = $element_id;

			$this->fk_object = $fk_object;

			$result = $this->create($user, false);
		}
		if ($result > 0 ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Clone an object into another one
	 *
	 * @param varchar $ref name of resource
	 * @param varchar $element_type type of resource
	 * @param int $element_id Id of resource
	 */
	function fetchSignatory($role = "", $fk_object)
	{
		return $this->fetchAll('', '', 0, 0, array('customsql' => 'fk_object = '.$fk_object .' AND '.'role = '.'"'.$role.'"'), 'AND');
	}
}

