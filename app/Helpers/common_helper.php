<?php

if (!function_usable('print_r2'))
{
	/**
	 * print_r2
	 *
	 * @param   mixed   $data
	 *
	 * @return  void
	 */
	function print_r2($data): void
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

if (!function_usable('cdn_url'))
{
	/**
	 * cdn_url
	 *
	 * @param   string  $path
	 * @param   bool    $useCdn
	 *
	 * @return  string
	 */
	function cdn_url(string $path, bool $useCdn = true): string
	{
		if ($useCdn)
			return '' . $path;
		else
			return $path;
	}
}
