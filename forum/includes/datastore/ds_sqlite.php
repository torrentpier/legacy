<?php

class datastore_sqlite extends datastore_common
{
	var $db  = null;
	var $cfg = array();

	function datastore_sqlite ($cfg)
	{
		$this->cfg = $cfg;
		$this->db = new sqlite_common($cfg);
	}

	function store ($title, $var)
	{
		$this->data[$title] = $var;

		$ds_title = sqlite_escape_string($title);
		$ds_data  = sqlite_escape_string(serialize($var));

		$result = $this->db->query("
			REPLACE INTO ". $this->cfg['table_name'] ."
				(ds_title, ds_data)
			VALUES
				('$ds_title', '$ds_data')
		");

		return (bool) $result;
	}

	function clean ()
	{
		$this->db->query("DELETE FROM ". $this->cfg['table_name']);
	}

	function _fetch_from_store ()
	{
		if (!$items = $this->queued_items)
		{
			$src = $this->_debug_find_caller('enqueue');
			trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
		}

		array_deep($items, 'sqlite_escape_string');
		$items_list = join("','", $items);

		$result = $this->db->query("
			SELECT ds_title, ds_data
			FROM ". $this->cfg['table_name'] ."
			WHERE ds_title IN('$items_list')
		");

		foreach (sqlite_fetch_all($result) as $row)
		{
			$this->data[$row['ds_title']] = unserialize($row['ds_data']);
		}
	}
}