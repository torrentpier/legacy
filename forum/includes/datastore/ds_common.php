<?php

/**
* Datastore
*/
class datastore_common
{
	/**
	* √отова€ к употреблению data
	* array('title' => data)
	*/
	var $data = array();

	/**
	* —писок элементов, которые будут извлечены из хранилища при первом же запросе get()
	* до этого момента они став€тс€ в очередь $queued_items дл€ дальнейшего извлечени€ _fetch()'ем
	* всех элементов одним запросом
	* array('title1', 'title2'...)
	*/
	var $queued_items  = array();

	var $ds_dir        = 'datastore/';  // inside INC_DIR

	/**
	* 'title' => 'builder script name (without ext)' inside "includes/datastore" dir
	*/
	var $known_items = array(
		'cat_forums'             => 'build_cat_forums',
		'jumpbox'                => 'build_cat_forums',
		'viewtopic_forum_select' => 'build_cat_forums',
		'latest_news'            => 'build_cat_forums',
		'ads'                    => 'build_cat_forums',
		'moderators'             => 'build_moderators',
		'stats'                  => 'build_stats',
		'ranks'                  => 'build_ranks',
		'attach_extensions'      => 'build_attach_extensions',
		'smile_replacements'     => 'build_smilies',
	);

	/**
	* Constructor
	*/
	function datastore_common () {}

	/**
	* @param  array(item1_title, item2_title...) or single item's title
	* @param  bool
	*/
	function enqueue ($items, $ignore_duplicate = false)
	{
		$cur_items = array_flip($this->queued_items);

		foreach ((array) $items as $item)
		{
			if (isset($cur_items[$item]))
			{
				if ($ignore_duplicate)
				{
					continue;
				}
				$src = $this->_debug_find_caller('enqueue');
				trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
			}
			$this->queued_items[] = $item;
		}
	}

	function &get ($title)
	{
		if (!isset($this->data[$title]))
		{
			$this->enqueue($title, true);
			$this->_fetch();
		}
		return $this->data[$title];
	}

	function store ($title, $var) {}

	function rm ($items)
	{
		foreach ((array) $items as $item)
		{
			unset($this->data[$item]);
		}
	}

	function update ($items)
	{
		foreach ((array) $items as $item)
		{
			$this->_build_item($item);
		}
	}

	function clean () {}

	function _fetch ($force_fetch = false)
	{
		$this->_fetch_from_store();

		foreach ($this->queued_items as $title)
		{
			if (!isset($this->data[$title]))
			{
				$this->_build_item($title);
			}
		}

		$this->queued_items = array();
	}

	function _fetch_from_store () {}

	function _build_item ($title)
	{
		if (!empty($this->known_items[$title]))
		{
			require(INC_DIR . $this->ds_dir . 'data/' . $this->known_items[$title] .'.php');
		}
		else
		{
			trigger_error("Datastore: don't know how to build '$title'", E_USER_ERROR);
		}
	}

	/**
	* Find caller source
	*/
	function _debug_find_caller ($function)
	{
		$source = '';

		foreach (debug_backtrace() as $trace)
		{
			if ($trace['function'] == $function)
			{
				$source = hide_bb_path($trace['file']) .'('. $trace['line'] .')';
				break;
			}
		}

		return $source;
	}
}

