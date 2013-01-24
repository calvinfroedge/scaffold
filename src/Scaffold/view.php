<?php

namespace Scaffold;

/*
* A Class for Handling Views
*/
class ScaffoldView
{
	private $dir;

	private $file;

	private $vars;

	public function __construct($dir = "views", $file = "", array $vars = array())
	{
		$this->dir = $dir;
		$this->vars = (object) array();
		$this->setVars($vars);
		$this->file = $file;
		return $this;
	}

	/*
	*	Set the Directory in Which the View Resides
	*/
	public function setDir($dir)
	{
		$this->dir = $dir;
		return $this;
	}

	public function setFile($file)
	{
		$this->file = $file;
		return $this;
	}

	public function appendVar($k, $v)
	{
		if(!isset($this->vars->$k)) throw new ScaffoldViewException("You cannot append to a var which is not set.");

		if(is_array($this->vars->$k)) {
			array_push($this->vars->$k, $v);
		}
		else {
			$this->vars->$k .= $v;
		}

		return $this;
	}

	public function setVar($k, $v)
	{
		$this->vars->$k = $v;
		return $this;
	}

	public function setVars(array $vars = array())
	{
		foreach($vars as $k=>$v) $this->setVar($k, $v);
		return $this;
	}

	public function getVars()
	{
		return $this->vars;
	}

	public function __toString()
	{
		return $this->render();
	}

	/*
	*	Render the view
	*/
	public function render()
	{
		if(!file_exists($this->dir.$this->file) && !file_exists($this->dir.$this->file.'.php')) throw new ScaffoldViewException("The view you are attempting to render does not exist.");
		ob_start();
		
		$embedded_views = array();
		foreach($this->vars as $k=>$v)
		{
			if(is_object($v) && $v instanceof ScaffoldView)
			{
				$embedded_views[$k] = $v;
				unset($this->vars->$k);
			}
		}

		$json  = json_encode($this->vars);
		$array = array_merge(json_decode($json, true), $embedded_views);
		extract($array);
		$noExt = include ($this->dir.$this->file);
		if(!$noExt){
			include($this->dir.$this->file.'.php');
		}
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
