<?php

class datastore_memcached extends datastore_common
{
	var $cfg       = null;
	var $memcache  = null;
	var $connected = false;

	function datastore_memcached ($cfg)
	{
		global $bb_cfg;

		if (!$this->is_installed())
		{
			die('Error: Memcached extension not installed');
		}

		$this->cfg = $cfg;
		$this->memcache = new Memcache;
	}
	
	function connect ()
	{
		$connect_type = ($this->cfg['pconnect']) ? 'pconnect' : 'connect';

		if (@$this->memcache->$connect_type($this->cfg['host'], $this->cfg['port']))
		{
			$this->connected = true;
		}

		if (DBG_LOG) dbg_log(' ', 'CACHE-connect'. ($this->connected ? '' : '-FAIL'));

		if (!$this->connected && $this->cfg['con_required'])
		{
			die('Could not connect to memcached server');
		}
	}

	function store ($title, $var, $ttl = 180)
	{
		if (!$this->connected) $this->connect();
		$this->data[$title] = $var;
		return (bool) $this->memcache->set($title, $var, 0, $ttl);
	}

	function clean ()
	{
		if (!$this->connected) $this->connect();
		foreach ($this->known_items as $title => $script_name)
		{
			$this->memcache->delete($title, 0);
		}
	}

	function _fetch_from_store ()
	{
		if (!$items = $this->queued_items)
		{
			$src = $this->_debug_find_caller('enqueue');
			trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
		}

		if (!$this->connected) $this->connect();
		foreach ($items as $item)
		{
			$this->data[$item] = $this->memcache->get($item);
		}
	}
	
	function is_installed ()
	{
		return class_exists('Memcache');
	}	
}