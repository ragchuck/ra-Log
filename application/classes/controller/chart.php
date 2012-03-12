<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart extends Controller_Base {

	public $data = array();

	public function action_day()
	{
		$param = $this->request->param();

		$d = new Model_Data;
		$db_result = $d->select()
			->where(DB::expr('DATE(ch_datetime)'), '=', date('Y-m-d', $this->time))
			->where('ch_key', '=', 'Pac')
			->find_all()
			->as_array();


		$data = array();
		foreach ($db_result as $obj)
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

		$this->data['chart']['title']['text'] = __("Daychart");
		$this->data['chart']['subtitle']['text'] = strftime("%#d. %B %Y", $this->time);
		$this->data['chart']['series'] = $series;
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

	public function after()
	{
		$chart_type = $this->request->action();

		if ($chart_type != 'total')
		{
			$prev = strtotime("-1 $chart_type", $this->time);
			$next = strtotime("+1 $chart_type", $this->time);
			$this->data['pager'] = array(
				'prev' => array(
					'href' => strftime("#!/chart/$chart_type/%Y/%m/%d", $prev),
					'text' => strftime("%#d. %b %Y", $prev)
				),
				'next' => array(
					'href' => strftime("#!/chart/$chart_type/%Y/%m/%d", $next),
					'text' => strftime("%#d. %b %Y", $next)
				)
			);
		}

		$this->data['chart']['type'] = $chart_type;
		$this->json($this->data);
		//$this->data['container_id'] = sha1($chart_type.$this->time);
		parent::after();
	}

}