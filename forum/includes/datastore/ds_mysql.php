<?php

class datastore_mysql extends datastore_common
{
	function store ($title, $var)
	{
		$this->data[$title] = $var;

		$args = $GLOBALS['db']->build_array('INSERT', array(
			'ds_title' => (string) $title,
			'ds_data'  => (string) serialize($var),
		));
		$GLOBALS['db']->query("REPLACE INTO ". DATASTORE_TABLE . $args);
	}

	function clean ()
	{
		$GLOBALS['db']->query("DELETE FROM ". DATASTORE_TABLE);
	}

	function _fetch_from_store ()
	{
		function escape_sql($str)
		{
			$GLOBALS['db']->escape_string($str);
		}
		if (!$items = $this->queued_items)
		{
			$src = $this->_debug_find_caller('enqueue');
			trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
		}

		array_deep($items, 'escape_sql');
		$items_list = join("','", $items);

		$sql = "SELECT SQL_CACHE ds_title, ds_data FROM ". DATASTORE_TABLE ." WHERE ds_title IN('$items_list')";

		foreach ($GLOBALS['db']->fetch_rowset($sql) as $row)
		{
			$this->data[$row['ds_title']] = unserialize($row['ds_data']);
		}
	}
}