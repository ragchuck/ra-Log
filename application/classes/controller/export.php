<?php
defined('SYSPATH') or die('No direct script access.');
/*
 *  @TODO Licence...
 */


/**
 * This file is part of the exporting module for Highcharts JS.
 * www.highcharts.com/license
 *
 *
 * Available POST variables:
 *
 * $tempName string The desired filename without extension
 * $type string The MIME type for export.
 * $width int The pixel width of the exported raster image. The height is calculated.
 * $svg string The SVG source code to convert.
 */


/**
 * Description of export
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Controller_Export extends Controller {

	public static $batik_path = 'batik-rasterizer.jar';

	public function action_index()
	{

		$param = $this->request->param();
		$type = $param['type'];
		$svg = $param['svg'];
		$filename = Arr::get($param, 'filename', 'chart');

		if (get_magic_quotes_gpc())
		{
			$svg = stripslashes($svg);
		}

		$tempName = md5(rand());


		// allow no other than predefined types
		switch ($type)
		{
			case 'image/png':
				$typeString = '-m image/png';
				$ext = 'png';
				break;
			case 'image/jpeg':
				$typeString = '-m image/jpeg';
				$ext = 'jpg';
				break;
			case 'application/pdf':
				$typeString = '-m application/pdf';
				$ext = 'pdf';
				break;
			case 'image/svg+xml':
				$ext = 'svg';
				break;
			default:
				throw new Kohana_Exception("Invalid type");
		}

		$outfile = "temp/$tempName.$ext";

		if ($ext == 'svg')
		{
			$this->response->headers("Content-Disposition",
				"attachment; filename=$filename.$ext");
			$this->response->headers("Content-Type", $type);
			$this->response->body($svg);
		}
		else
		{

			// size
			if (isset($param['width']))
			{
				$width = (int) $param['width'];
				if ($width)
					$width = "-w $width";
			}

			// generate the temporary file
			if ( ! file_put_contents("temp/$tempName.svg", $svg))
			{
				throw new Kohana_Exception("Couldn't create temporary file.");
			}

			// do the conversion
			$output = shell_exec("java -jar ".BATIK_PATH." $typeString -d $outfile $width temp/$tempName.svg");

			// catch error
			if ( ! is_file($outfile) || filesize($outfile) < 10)
			{
				throw new Kohana_Exception("Error while converting SVG: ".$output);
			}

			// stream it
			$this->response->headers("Content-Disposition",
				"attachment; filename=$filename.$ext");
			$this->response->headers("Content-Type", $type);
			$this->response->body(file_get_contents($outfile));


			// delete it
			unlink("temp/$tempName.svg");
			unlink($outfile);

			// SVG can be streamed directly back
		}
	}

}

?>
