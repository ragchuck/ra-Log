<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Chart extends Controller_Base {


	public function action_day()
	{

		$param = $this->request->param();

		$d = new Model_Data;
		$result = $d->select()
			->where(DB::expr('DATE(ch_datetime)'), '=', date('Y-m-d',(double)$this->time))
			->where('ch_key', '=', 'Pac')
			->find_all();


		$data = array();
		foreach ($result->as_array() as $obj)
		{
			$data[] = $obj->to_array();
		}


		$series = array(
			array(
				'type' => 'area',
				'name' => 'Pac#1',
				'data' => $data
			)
		);

		$chart_name = __("Daychart");

		$view = View::factory('chart.tpl');
		$view->chart_type = 'day';
		$view->time= $this->time;
		$view->container_id= sha1($this->time);
		$view->series= $series;
		$view->chart_name = $chart_name;
		$view->caption = $chart_name;

		$this->response->body($view);
	}

	public function action_week()
	{

	}

	public function action_month()
	{

	}

	public function action_year()
	{

	}

	public function action_total()
	{

	}




}