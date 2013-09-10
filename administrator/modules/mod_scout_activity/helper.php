<?php 
defined('_JEXEC') or die('Restricted Access');
jimport( 'joomla.application.component.model' );

class modScoutActivityHelper 
{
	function isInstalled()
	{
		$success = false;

		jimport( 'joomla.filesystem.file' );
		$filePath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'scout.php';
		if (JFile::exists($filePath))
		{
			$success = true;
		}			
		return $success;
	}
		
	function getSiteWideActivity($limit) 
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'models' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
		
		$model = JModel::getInstance( 'Logs', 'ScoutModel' );
        $model->setState( 'order', 'tbl.datetime' );
        $model->setState( 'direction', 'DESC' );        		
		$model->setState( 'limit', $limit );
		return $model->getList();
	}
}
?>