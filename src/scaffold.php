<?php

require 'template.php';
require 'view.php';
require 'exception/template_exception.php';
require 'exception/view_exception.php';

/*
* Multiton class which serves as a facade to the Template / View Classes
*/
class Scaffold
{
	private static $instances = array();

	private $views;

	private $templates;

	private $template;

	private $viewDir;

	/*
	*	Multiton constructor must be private
	*/
	private function __construct()
	{
		$this->templates = (object) array();

		$this->views = (object) array();
	}

	public static function instance($name = "default")
	{
		if(empty(self::$instances)){
			$instance = self::$instances[$name] = new self;
		}

		return $instance;
	}

	/*
	*	Assign a View to a Template
	*/
	public function assignViewToTemplate($view, $template, $order = false)
	{
		if(!isset($this->templates->$template, $this->views->$view)){
			if(!isset($this->views->$view)) throw new ScaffoldViewException("$view is not a view.");
			if(!isset($this->templates->$template)) throw new ScaffoldViewException("$template is not a view.");
		}

		$this->templates->$template->setView($view, $this->views->$view);

		if($order){
			$this->templates->$template->setView($view, $this->views->$view, $order);
		}
	}

	/*
	*	Create a new template. Alias for ScaffoldTemplate constructor.
	*/
	public function templateCreate($template)
	{
		if(isset($this->templates->$template)){
			throw new ScaffoldTemplateException("The template you are attempting to create already exists.");
		}

		$this->templates->$template = new ScaffoldTemplate($template);

		//Return the new instance so we can chain metthods to it
		return $this->templates->$template;
	}

	/*
	*	Copies an existing template instance
	*/
	public function templateCopy($existing, $new, $type = "shallow")
	{
		if(!isset($this->templates->$existing)){
			throw new ScaffoldTemplateException("The template you specified does not exist!");
		}

		if($type == 'deep'){
			$this->templates->$new = unserialize(serialize($this->templates->$existing)); //Hack for doing a deep copy
		}
		else if($type == 'shallow'){
			$this->templates->$new = clone $this->templates->$existing;
		}
		else {
			throw new Exception("The copy type you specified $type does not exist.");
		}

		return $this->templates->$new;
	}

	/*
	*	Get a previously defined template
	*/
	public function templateGet($template)
	{
		if(!isset($this->templates->$template)){
			throw new ScaffoldTemplateException("The template you are attempting to get does not exist.");
		}

		return $this->templates->$template;
	}

	/*
	*	Get all templates
	*/
	public function templatesGet()
	{
		return $this->templates;
	}

	/*
	*	Create a new named view.  Alias for ScaffoldView constructor.
	*/
	public function viewCreate($view, $file = "", $vars = array())
	{
		if(isset($this->views->$view)){
			throw new ScaffoldViewException("The view you are attempting to create already exists.");
		}

		$newView = new ScaffoldView($this->viewDir, $file, $vars);
		$newView->setDir($this->viewDir);

		$this->views->$view = $newView;
		
		return $this->views->$view;
	}

	/*
	*	Get a named view.
	*/
	public function viewGet($view)
	{
		if(!isset($this->views->$view)){
			throw new ScaffoldViewException("The view you are attempting to get does not exist.");
		}

		return $this->views->$view;
	}

	/*
	*	Get all defined views.
	*/
	public function viewsGet()
	{
		return $this->views;
	}

	/*
	*	Set the view directory.
	*/
	public function setViewDir($dir)
	{
		$this->viewDir = $dir;
		return $this;
	}

	/*
	* Get the view directory
	*/
	public function getViewDir()
	{
		return $this->viewDir;
	}

	/*
	*	Render the template
	*/
	public function render($template = "default")
	{
		if(!isset($this->templates->$template)){
			throw new ScaffoldTemplateException("The template you are attempting to get does not exist.");
		}

		echo $this->templates->$template->render();
	}
}
