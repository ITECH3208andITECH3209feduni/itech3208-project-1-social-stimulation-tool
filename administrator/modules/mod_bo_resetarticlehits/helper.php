<?php
/**
* @Copyright Copyright (C) 2012 Alfred Bösch
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// No direct access.
defined('_JEXEC') or die;

JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_content/models', 'ContentModel');

jimport('joomla.application.categories');

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_bo_resetarticlehits
 */
abstract class modBoResetArticleHitsHelper
{
	/**
	 * Get a list of all articles
	 *
	 * @param	JObject		The module parameters.
	 *
	 * @return	array
	 */
	public static function getList($params)
	{
		$jinput = JFactory::getApplication()->input;

		$articleId = $jinput->get('articleId', 0);
		$newArticleHits = $jinput->get('newArticleHits', 0, 'array');
		if ($articleId != 0) {
			$db	= JFactory::getDbo();
			$query = "UPDATE #__content SET hits=".round($newArticleHits[$articleId])." WHERE id=".$articleId;
			$db->setQuery($query);
			$db->query();
			$jinput->set('articleId', 0);
		}
		
		// Initialise variables
		$user = JFactory::getuser();

		// Get an instance of the generic articles model
		$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set List SELECT
		$model->setState('list.select', 'a.id, a.title, a.checked_out, a.checked_out_time, ' .
				' a.created, a.hits');

		// Set Ordering filter
		$filterOrder = trim($jinput->get('filter_order', 'a.title'));
		$filterOrderDir = trim($jinput->get('filter_order_Dir', 'ASC'));
		$model->setState('list.ordering', $filterOrder);
		$model->setState('list.direction', $filterOrderDir);

		// Set Category Filter
		$categoryFilter = trim($jinput->get('categoryFilter', 0));
		if ($categoryFilter != 0) {
			$model->setState('filter.category_id', $categoryFilter);
		}
		
		// Set Search Filter
		$searchFilter = trim($jinput->get('searchFilter', ''));
		if ($searchFilter != '') {
			$model->setState('filter.search', $searchFilter);
		}
		
		// Set Published Filter
		$publishedFilter = trim($jinput->get('publishedFilter', ''));
		if ($publishedFilter != '') {
			$model->setState('filter.published', $publishedFilter);
		}
		
		// Set Language Filter
		$languageFilter = trim($jinput->get('languageFilter', ''));
		if ($languageFilter != '') {
			$model->setState('filter.language', $languageFilter);
		}

		// Set User Filter.
		$userId = $user->get('id');
		switch ($params->get('user_id')) {
			case 'by_me':
				$model->setState('filter.author_id', $userId);
				break;

			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;
		}

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);

		$items = $model->getItems();

		if ($error = $model->getError()) {
			JError::raiseError(500, $error);
			return false;
		}

		// Set the links
		foreach ($items as &$item) {
			if ($user->authorise('core.edit', 'com_content.article.'.$item->id)){
				$item->link = JRoute::_('index.php?option=com_content&task=article.edit&id='.$item->id);
			} else {
				$item->link = '';
			}
		}

		return $items;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param	JObject	The module parameters.
	 * @return	string	The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who = $params->get('user_id');
		$catid = (int)$params->get('catid');
		if ($catid)
		{
			$category = JCategories::getInstance('Content')->get($catid);
			if ($category) {
				$title = $category->title;
			}
			else {
				$title = JText::_('MOD_BO_RESETARTICLEHITS_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}
		return JText::plural('MOD_BO_RESETARTICLEHITS_TITLE'.($catid ? "_CATEGORY" : '').($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}