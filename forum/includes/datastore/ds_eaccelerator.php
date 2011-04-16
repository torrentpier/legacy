<?php

class datastore_eaccelerator extends datastore_common
{
	function store ($title, $var)
	{
		$this->data[$title] = $var;
		eaccelerator_put($title, $var);
	}

	function clean ()
	{
		foreach ($this->known_items as $title => $script_name)
		{
			eaccelerator_rm($title);
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
			$this->data[$item] = eaccelerator_get($item);
		}
	}
}