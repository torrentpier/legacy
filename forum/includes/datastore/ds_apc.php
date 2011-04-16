<?php

class datastore_apc extends datastore_common
{
	function store ($title, $var)
	{
		$this->data[$title] = $var;
		return (bool) apc_store($title, $var);
	}

	function clean ()
	{
		foreach ($this->known_items as $title => $script_name)
		{
			apc_delete($title);
		}
	}

	function _fetch_from_store ()
	{
		if (!$items = $this->queued_items)
		{
			$src = $this->_debug_find_caller('enqueue');
			trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
		}

		foreach ($items as $item)
		{
			$this->data[$item] = apc_fetch($item);
		}
	}
}