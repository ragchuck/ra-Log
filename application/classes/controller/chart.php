<?php
defined('SYSPATH') or die('No direct script access.');


class Controller_Chart extends Controller_Base {

	public $data = array();

	/**
	 *
	 * @var int UTC
	 */
	public $time;

	public function before()
	{
		parent::before();

		$param = $this->request->param();

		// Extract the time
            // Time (year is a unix timestamp)
            if (!empty($param['year']) AND strlen($param['year']) > 4)
            {
                  $this->time = $param['year'];
            }
            elseif (!empty($param['year']))
            {
                  $this->time = gmmktime(0, 0, 0,
                        Arr::get($param, 'month', 1),
                        Arr::get($param, 'day', 1),
                        Arr::get($param, 'year'));
            }
            else
            {
                  $this->time = time();
            }
	}

	public function action_day()
	{
		//$param = $this->request->param();

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
		$this->data['chart']['subtitle']['text'] = utf8_encode(strftime("%#d. %B %Y", $this->time));
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
					'text' => strftime("&larr; %#d. %b %Y", $prev)
				),
				'next' => array(
					'href' => strftime("#!/chart/$chart_type/%Y/%m/%d", $next),
					'text' => strftime("%#d. %b %Y &rarr;", $next)
				)
			);

                  // cache only if current chart is present
                  if ($next < time()) {
                        // enable Browser caching
                        $this->enable_browser_caching();
                  }
		}
            else {
                  $this->data['pager'] = false;
            }

		$this->data['chart']['type'] = $chart_type;
		$this->json($this->data);
		//$this->data['container_id'] = sha1($chart_type.$this->time);
		parent::after();
	}

}