<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Chartoption extends Controller_Base {


	public function action_get()
	{
		$id = $this->request->param('id');
		$template = View::factory('chart.tpl');
		$template->chart_js = "chart/$id.js";
		$template->chart_type = $id;
		$template->container_id = sha1("container-$id");
		$this->response->body($template);
	}
}
