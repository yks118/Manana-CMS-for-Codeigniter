<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class google_api {
	private $key = '';
	private $key_file = '';
	private $client;
	
	public function __construct () {
		require_once APPPATH.'libraries/google_api/vendor/autoload.php';
		
		$this->key_file = APPPATH.'libraries/google_api/cms analytics api-e361938286eb.json';
		
		$this->client = new Google_Client();
	}
	
	/**
	 * _analytics_request
	 * 
	 * get analytics request
	 * 
	 * @param	numberic	$view_id
	 * @param	object		$dateRange
	 * @param	string		$metric
	 * @param	array		$dimension
	 */
	private function _analytics_request ($view_id,$dateRange,$metric = '',$dimension = array()) {
		$dimensions = $dimensions_data = array();
		
		if (is_array($dimension)) {
			$dimensions = $dimension;
		} else {
			$dimensions[] = $dimension;
		}
		
		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($view_id);
		$request->setDateRanges($dateRange);
		
		if (!empty($metric)) {
			// Create the Metrics object.
			$metrics = new Google_Service_AnalyticsReporting_Metric();
			$metrics->setExpression('ga:'.$metric);
			$metrics->setAlias($metric);
			
			$request->setMetrics(array($metrics));
		}
		
		if (!empty($dimensions)) {
			foreach ($dimensions as $key => $dimension) {
				$dimensions_report = new Google_Service_AnalyticsReporting_Dimension();
				$dimensions_report->setName('ga:'.$dimension);
				
				$dimensions_data[] = $dimensions_report;
			}
			
			$request->setDimensions($dimensions_data);
		}
		
		return $request;
	}
	
	/**
	 * _sort
	 * 
	 * value로 sort
	 */
	private function _sort ($data,$type = 'desc') {
		$list = $tmp = array();
		
		foreach ($data as $report => $row) {
			$tmp = array();
			
			for ($i = 0; $i < count($row); $i++) {
				$tmp[$i] = $row[$i]['data'];
			}
			
			if ($type == 'asc') {
				asort($tmp);
			} else {
				arsort($tmp);
			}
			
			foreach ($tmp as $key => $value) {
				$list[$report][] = $row[$key];
			}
		}
		
		return $list;
	}
	
	/**
	 * analytics
	 * 
	 * https://developers.google.com/analytics/devguides/reporting/realtime/dimsmets/
	 * 
	 * @param	numberic	$view_id
	 * @param	array		$names
	 * @param	string		$startData
	 * @param	string		$endData
	 */
	public function analytics ($view_id,$names,$startData = '7daysAgo',$endData = 'today') {
		$analytics;
		$start_data = $end_data = $now_data = '';
		$request = $data = $reports = $dataRange = $tmp = array();
		
		$this->client->setApplicationName('Manana CMS Analytics');
		$this->client->setAuthConfig($this->key_file);
		$this->client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
		$analytics = new Google_Service_AnalyticsReporting($this->client);
		
		// Create the DateRange object.
		$dateRange = new Google_Service_AnalyticsReporting_DateRange();
		$dateRange->setStartDate($startData);
		$dateRange->setEndDate($endData);
		
		foreach ($names as $key => $name) {
			switch ($name) {
				case 'sessions' :
				case 'visitors' :
						$request[] = $this->_analytics_request($view_id,$dataRange,$name);
					break;
				case 'browser' :
				case 'keyword' :
				case 'country' :
				case 'trafficType' :
				case 'source' :
				case 'browserSize' :
				case 'mobileDeviceInfo' :
				case 'mobileDeviceBranding' :
				case 'deviceCategory' :
				case 'userType' :
						$request[] = $this->_analytics_request($view_id,$dateRange,'',$name);
					break;
				case 'browserVersion' :
						$request[] = $this->_analytics_request($view_id,$dateRange,'',array('browser','browserVersion'));
					break;
				case 'referral' :	// full path
						$request[] = $this->_analytics_request($view_id,$dateRange,'',array('source','referralPath'));
					break;
				case 'page' :	// page info
						$request[] = $this->_analytics_request($view_id,$dateRange,'pageviews',array('pageTitle','pagePath'));
					break;
			}
		}
		
		$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
		$body->setReportRequests($request);
		$reports = $analytics->reports->batchGet($body);
		
		foreach ($reports as $key => $report) {
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();
			
			foreach ($rows as $row) {
				$tmp = array();
				
				$tmp['dimensions'] = $row->getDimensions();
				$metrics = $row->getMetrics();
				
				for ($j = 0; $j < count( $metricHeaders ) && $j < count( $metrics ); $j++) {
					$entry = $metricHeaders[$j];
					$values = $metrics[$j];
					
					for ( $valueIndex = 0; $valueIndex < count( $values->getValues() ); $valueIndex++ ) {
						$value = $values->getValues();
						
						$tmp['data'] = $value[$valueIndex];
					}
				}
				
				$data[$names[$key]][] = $tmp;
			}
		}
		
		return $this->_sort($data);
	}
}