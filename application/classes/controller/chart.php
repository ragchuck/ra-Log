<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Chart extends Controller {

	public function action_build()
	{
		$param = $this->request->param();

		// Type
		if (array_key_exists('type', $param))
		{
			$type = $param['type'];
		}
		elseif (array_key_exists('day', $param))
		{
			$type = 'day';
		}
		elseif (array_key_exists('month', $param))
		{
			$type = 'month';
		}
		elseif (array_key_exists('year', $param))
		{
			$type = 'year';
		}
		else
		{
			$type = 'day';
		}


		$d = new Model_Data;
		$result = $d->select()
			->where(DB::expr('DATE(ch_datetime)'), '=', date('Y-m-d',$this->_time))
			->where('ch_key', '=', 'Pac')
			->find_all();


		$data = array();
		foreach ($result->as_array() as $obj)
		{
			$data[] = $obj->to_array();
		}

		Fire::fb($d);
		Fire::fb($result);
		Fire::fb($data);

		$series = array(
			array(
				'type' => 'area',
				'name' => 'Pac#1',
				'data' => $data
			)
		);

		$chart_name = "Test";

		$view = new View('chart');
		$view->set('time', $this->_time);
		$view->set('container_id', sha1($type.$this->_time));
		$view->set('series', $series);
		$view->set('chart_name', $chart_name);
		$view->set('caption', $chart_name);

		$this->response->body($view);
	}

}