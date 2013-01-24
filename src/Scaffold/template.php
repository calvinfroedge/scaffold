<?php

/*
* A Collection of Views Which Form a Template
*/
class ScaffoldTemplate
{
	private $name;

	private $views;

	public function __construct($name)
	{
		$this->name = $name;
		$this->views = (object) array();
		return $this;
	}

	/*
	* Add a view to the template, or update an existing view
	*/
	public function setView($name, ScaffoldView $view, $order = false)
	{
		if($order){
			if(!is_array($order)) throw new ScaffoldTemplateException("Order clause for setView must be an array!");

			list($type, $reference) = $order;

			$temp = array();
			$setter = function($setType, $reference, $name, $view, &$temp, $views)
			{
				$found = 0;
				foreach($views as $k=>$v)
				{
					if($k == $reference && !$found)
					{
						if($k == $reference && $setType == 'after') $temp[$k] = $v;
						if($k == $reference) $temp[$name] = $view;
						if($k == $reference && $setType == 'before') $temp[$k] = $v;
						++$found;
					}
					else
					{
						$temp[$k] = $v;
					}
				}
			};

			switch($type)
			{
				case ($type == 'after' || $type == 'before'):
					$setter($type, $reference, $name, $view, $temp, $this->views);
				break;

				case 'head':
					array_unshift($this->views, $view);
				break;

				case 'tail':
					array_push($this->views, $view);
				break;

				default:
					throw new ScaffoldTemplateException("No matching order option found for type $type");
				break;
			}

			$this->views = (object) $temp;
		}
		else {
			$this->views->$name = $view;
		}

		return $this;
	}

	/*
	* Remove a view from the template
	*/
	public function removeView($name)
	{
		unset($this->views->$name);
		return $this;
	}

	/*
	* Get all views
	*/
	public function getViews()
	{
		return $this->views;
	}

	/*
	* Build up the output (all the contained views) and return
	*/
	public function render()
	{
		$output = "";
		foreach($this->views as $k=>$v) $output .= $v->render();
		return $output;
	}
}
