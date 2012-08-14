<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Helper for the inofficial Google Weather-API.
 *
 * @package    Application
 * @category   Class
 * @author     Leonard Fischer <leonard.fischer@sn4ke.de>
 * @copyright  (c) 2011
 */
class GWeather
{
	/**
	 * A slightly modified method for fetching weather results,
	 * found at http://www.web-spirit.de/webdesign-tutorial/9/Wetter-auf-eigener-Website-mit-Google-Weather-API
	 * @param  string $city
	 * @param  string $language
	 * @return array
	 * @uses   Kohana::cache
	 */
	public static function get_data($city, $language = 'de')
	{
		$aReturn = Kohana::cache('weather-api', NULL, 3600);

		if ($aReturn === NULL)
		{
			$aReturn = array();
			$oXml = simplexml_load_string(utf8_encode(file_get_contents('http://www.google.com/ig/api?weather=' . $city . '&hl=' . $language)));

			$aReturn['city'] = (String) $oXml->weather->forecast_information->city->attributes()->data;
			$aReturn['date'] = (String) $oXml->weather->forecast_information->forecast_date->attributes()->data;
			$aReturn['time'] = (String) $oXml->weather->forecast_information->current_date_time->attributes()->data;

			$aReturn['now']['condition'] = (String) $oXml->weather->current_conditions->condition->attributes()->data;
			$aReturn['now']['temperature'] = (String) $oXml->weather->current_conditions->temp_c->attributes()->data;
			$aReturn['now']['humidity'] = (String) $oXml->weather->current_conditions->humidity->attributes()->data;
			$aReturn['now']['wind'] = (String) $oXml->weather->current_conditions->wind_condition->attributes()->data;
			$aReturn['now']['icon'] = str_replace('/ig/images/weather/', '', $oXml->weather->current_conditions->icon->attributes()->data);

			$i = 1;
			foreach($oXml->weather->forecast_conditions as $oWeather)
			{
				$aReturn['days'][$i]['weekday'] = (String) $oWeather->day_of_week->attributes()->data;
				$aReturn['days'][$i]['condition'] = (String) $oWeather->condition->attributes()->data;
				$aReturn['days'][$i]['temperature_min'] = (String) $oWeather->low->attributes()->data;
				$aReturn['days'][$i]['temperature_max'] = (String) $oWeather->high->attributes()->data;
				$aReturn['days'][$i]['icon'] = str_replace('/ig/images/weather/', '', $oWeather->icon->attributes()->data);

				$i++;
			} // foreach

			Kohana::cache('weather-api', $aReturn);
		} // if

		return $aReturn;
	} // function
} // class