<?php namespace App\Format;

use CodeIgniter\Format\Exceptions\FormatException;
use CodeIgniter\Format\FormatterInterface;

/**
 * Class CSVFormatter
 *
 * @package App\Format
 */
class CSVFormatter implements FormatterInterface
{
	public function format($data)
	{
		$delimiter = ',';
		$enclosure = '"';

		$fp = fopen('php://memory', 'r+b');
		// set UTF-8
		fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
		if (is_array(array_values($data)[0]))
		{
			foreach ($data as $key => $row)
			{
				if ($key === 0)
					fputcsv($fp, array_keys($row), $delimiter, $enclosure, "\0");
				fputcsv($fp, array_values($row), $delimiter, $enclosure, "\0");
			}
		}
		else
		{
			fputcsv($fp, array_keys($data), $delimiter, $enclosure, "\0");
			fputcsv($fp, array_values($data), $delimiter, $enclosure, "\0");
		}
		rewind($fp);
		$result = stream_get_contents($fp);
		fclose($fp);
		return $result;
	}
}
