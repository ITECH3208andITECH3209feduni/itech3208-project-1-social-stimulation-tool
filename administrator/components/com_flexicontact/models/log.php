<?php
/********************************************************************
Product		: Flexicontact
Date		: 11 October 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactModelLog extends JModelLegacy
{
var $_data;
var $app = null;
var $_pagination = null;

function __construct()
{
	parent::__construct();
	$this->app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// this is only used for back end test emails
//
function init_data($from_name, $from_email, $admin_email, $subject, $message, $return_info)
{
	$this->data = new stdclass();
	$this->data->admin_from_email = $from_email;
	$this->data->admin_email = $admin_email;
	$this->data->admin_email_subject = $subject;
	$this->data->admin_email_body = $message;
	$this->data->status_main = $return_info;
	$this->data->status_copy = '0';
}

//-------------------------------------------------------------------------------
// get an existing row
// return false if we couldn't find it
//
function getOne($id)
{
    $query = $this->_db->getQuery(true);
    $query->select(array('*'));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($this->_db->quoteName('id') . ' = '. $this->_db->quote($id));
    try
		{
		$this->_db->setQuery($query);
		$this->_data = $this->_db->loadObject();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        FC_trace::trace($this->ladb_error_text);
		return false;
		}
	return $this->_data;
}

//---------------------------------------------------------------
//
function store($data)
{
	if (isset($data->name))
		$data->name = substr($data->name, 0, LAFC_MAX_NAME_LENGTH);
	if (isset($data->email))
		$data->email = substr($data->email, 0, LAFC_MAX_EMAIL_LENGTH);
	if (isset($data->admin_email))
		$data->admin_email = substr($data->admin_email, 0, LAFC_MAX_EMAIL_LENGTH);	// from the menu item - can't validate that
	if (isset($data->admin_from_email))
		$data->admin_from_email = substr($data->admin_from_email, 0, LAFC_MAX_EMAIL_LENGTH);	// from Joomla Global config
	if (isset($data->subject))
		$data->subject = substr($data->subject, 0, LAFC_MAX_SUBJECT_LENGTH);	// old configs could have a default subject > max
	if (isset($data->status_main))
		$data->status_main = substr($data->status_main, 0, LAFC_MAX_VARCHAR_LENGTH);  // can be from PHPMAILER
	if (isset($data->status_copy))
		$data->status_copy = substr($data->status_copy, 0, LAFC_MAX_VARCHAR_LENGTH);  // can be from PHPMAILER
	if (isset($data->list_choice))
		$data->list_choice = substr($data->list_choice, 0, LAFC_MAX_VARCHAR_LENGTH);
	if (!isset($data->message))
		$data->message = '';
	if (!isset($data->admin_email_body))
		$data->admin_email_body = '';
	if (!isset($data->user_email_body))
		$data->user_email_body = '';

    $query = $this->_db->getQuery(true);
	$columns = array('datetime');	
	$values = array('NOW()');	
	foreach ($data as $name => $value)
		{
		if (substr($name,0,1) == '_')
			continue;
		$columns[] = $name;
		$values[] = $this->_db->Quote($data->$name);
		}
    $query->insert($this->_db->quoteName('#__flexicontact_log'));
    $query->columns($this->_db->quoteName($columns));
    $query->values(implode(',', $values));
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
        $result = true;
		}
	catch (\RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $result = false;
        FC_trace::trace("Log store SQL error ".$this->ladb_error_code."\n $query \n".$this->ladb_error_text);
		return $result;
		}
	FC_trace::trace("$query\nLog store OK");
	return $result;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// This should normally be called after getList()
//
function getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Get the list of logs for the log list screen
//
function getList()
{
// get the filter states, order states, and pagination variables

	$filter_date = $this->app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');
	$limit = $this->app->getUserStateFromRequest(LAFC_COMPONENT.'.log_limit', 'log_limit', $this->app->get('list_limit'), 'int');
	if ($limit > 500)
		$limit = 500;
	$limitstart = $this->app->getUserStateFromRequest(LAFC_COMPONENT.'.limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$filter_order = $this->app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'DATETIME');
	$filter_order_Dir = $this->app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'DESC');

// build the where clause

	$query_where = "1 ";

	switch ($filter_date)
		{
		case LAFC_LOG_ALL:
			break;
		case LAFC_LOG_LAST_7_DAYS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
			break;
		case LAFC_LOG_LAST_28_DAYS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 28 DAY)";
			break;
		case LAFC_LOG_LAST_12_MONTHS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)";
		}	

// order by

	if (strcasecmp($filter_order_Dir,'ASC') != 0)
		$filter_order_Dir = 'DESC';

	switch ($filter_order)
		{
		case 'NAME': 
			$query_order = $this->_db->quoteName('name').' '.$filter_order_Dir; 
			break;
		case 'EMAIL':
			$query_order = $this->_db->quoteName('email').' '.$filter_order_Dir; 
			break;
		case 'SUBJECT':			// not currently used because which subject should we sort on?
			$query_order = $this->_db->quoteName('admin_email_subject').' '.$filter_order_Dir; 
			break;
		default:
			$query_order = $this->_db->quoteName('datetime').' '.$filter_order_Dir; 
			break;
		}

// get the total row count

    $query = $this->_db->getQuery(true);
    $query->select(array('count(*)'));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($query_where);
	try
		{
		$this->_db->setQuery($query);
		$total = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $total = false;
		}
        
	if ($total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $total;
		}

// setup the pagination object

	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the data, within the limits

    $query = $this->_db->getQuery(true);
    $query->select(array(
        $this->_db->quoteName('id'),
        $this->_db->quoteName('datetime'),
        $this->_db->quoteName('name'),
        $this->_db->quoteName('email'),
        $this->_db->quoteName('subject'),
        $this->_db->quoteName('admin_email_subject'),
        'SUBSTRING('.$this->_db->quoteName('message').',1,60) AS short_message',
        $this->_db->quoteName('status_main'),
        $this->_db->quoteName('status_copy') ));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($query_where);
    $query->order($query_order);
    
    try
		{
		$this->_db->setQuery($query, $limitstart, $limit);
		$this->_data = $this->_db->loadObjectList();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $this->_data = false;
		}
	
	if ($this->_data === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->_data;
		}
		
	return $this->_data;
}

//-------------------------------------------------------------------------------
// delete a log entry
//
function delete($id)
{
    $query = $this->_db->getQuery(true);
    $query->delete($this->_db->quoteName('#__flexicontact_log'));
    $query->where(array($this->_db->quoteName('id').' = '.$this->_db->Quote($id)));
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
        $result = true;
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $result = false;
		}
    
	if ($result === false)
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
}

//---------------------------------------------------------------
// Purge expired log entries
// We don't purge specifically by config. Logs are purged to the least number of days in any active config.
//
function purge($config_data)
{
	$days = $config_data->log_keep_days;
	if ($days == 0)		// don't delete anything
		return;

    $query = $this->_db->getQuery(true);
	$query->delete($this->_db->quoteName('#__flexicontact_log'))
	      ->where('DATE(`datetime`) < (CURDATE() - INTERVAL '.$days.' DAY)');
	$this->_db->setQuery($query);

	try
		{
		$this->_db->execute();
		}
	catch (\Exception $e)
		{
		FC_trace::trace(" Failed to purge log records: ".$e->getMessage());
		}

	$rows_affected = $this->_db->getAffectedRows();

	FC_trace::trace("Purged $rows_affected log records");
}

}	