<?php
/**
 * EGroupware - TimeSheet - setup table updates
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package timesheet
 * @subpackage setup
 * @copyright (c) 2005-17 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 */

use EGroupware\Api;

function timesheet_upgrade0_1_001()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_timesheet','pl_id',array(
		'type' => 'int',
		'precision' => '4',
		'default' => '0'
	));

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '0.2.001';
}


function timesheet_upgrade0_2_001()
{
	$GLOBALS['egw_setup']->oProc->CreateTable('egw_timesheet_extra',array(
		'fd' => array(
			'ts_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'ts_extra_name' => array('type' => 'varchar','precision' => '32','nullable' => False),
			'ts_extra_value' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
		),
		'pk' => array('ts_id','ts_extra_name'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '0.2.002';
}


function timesheet_upgrade0_2_002()
{
	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.4';
}


function timesheet_upgrade1_4()
{
	// delete empty cf's generated by 1.4
	$GLOBALS['egw_setup']->db->delete('egw_timesheet_extra',"ts_extra_value=''",__LINE__,__FILE__,'timesheet');

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.6';
}


function timesheet_upgrade1_6()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_timesheet','ts_status',array(
		'type' => 'int',
		'precision' => '4'
	));

	$GLOBALS['egw_setup']->oProc->CreateIndex('egw_timesheet','ts_status');

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.7.001';
}


function timesheet_upgrade1_7_001()
{
	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.8';
}


function timesheet_upgrade1_8()
{
	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.9.001';
}


/**
 * Change titel and project title to varchar(255) to not loose content when creating a timesheet eg. from an InfoLog
 *
 * Change description to varchar(16384) to not force full table-scan on search
 *
 * @return string
 */
function timesheet_upgrade1_9_001()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet','ts_project',array(
		'type' => 'varchar',
		'precision' => '255',
		'comment' => 'project title'
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet','ts_title',array(
		'type' => 'varchar',
		'precision' => '255',
		'nullable' => False,
		'comment' => 'title of the timesheet entry'
	));
	// change description to varchar(16384), if there is no longer content already
	$max_description_length = $GLOBALS['egw']->db->query('SELECT MAX(CHAR_LENGTH(ts_description)) FROM egw_timesheet')->fetchColumn();
	// returns NULL, if there are no rows!
	if ((int)$max_description_length <= 16384 && $GLOBALS['egw_setup']->oProc->max_varchar_length >= 16384)
	{
		$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet','ts_description',array(
			'type' => 'varchar',
			'precision' => '16384',
			'comment' => 'description of the timesheet entry'
		));
	}
	return $GLOBALS['setup_info']['timesheet']['currentver'] = '1.9.002';
}


function timesheet_upgrade1_9_002()
{
	// switch history / delete prevention on, like for new installs, so only admins can finally delete timesheets
	Api\Config::save_value('history', 'history', 'timesheet');

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '14.1';
}


function timesheet_upgrade14_1()
{
	return $GLOBALS['setup_info']['timesheet']['currentver'] = '16.1';
}

function timesheet_upgrade16_1()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('egw_timesheet','ts_created',array(
		'type' => 'int',
		'meta' => 'timestamp',
		'precision' => '8',
		'nullable' => true
	));
	// Initialize to start
	$GLOBALS['egw']->db->query('UPDATE egw_timesheet set ts_created = ts_start');

	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet','ts_created',array(
		'type' => 'int',
		'meta' => 'timestamp',
		'precision' => '8',
		'nullable' => False
	));

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '17.1';
}

function timesheet_upgrade17_1()
{
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet_extra','ts_extra_name',array(
		'type' => 'varchar',
		'meta' => 'cfname',
		'precision' => '64',
		'nullable' => False
	));
	$GLOBALS['egw_setup']->oProc->AlterColumn('egw_timesheet_extra','ts_extra_value',array(
		'type' => 'varchar',
		'meta' => 'cfvalue',
		'precision' => '16384',
		'nullable' => False,
		'default' => ''
	));

	return $GLOBALS['setup_info']['timesheet']['currentver'] = '17.1.001';
}
