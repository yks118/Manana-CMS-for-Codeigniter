jQuery(function(){
	if (jQuery('#browserChart').length) {
		jQuery.plot('#browserChart',browserChart,{
			series: {
				pie: {
					show: true,
					stroke: {
						width: 2,
					},
				},
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			tooltip: true,
			tooltipOpts: {
				content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
				shifts: {
					x: 20,
					y: 0
				},
				defaultTheme: false,
				cssClass: 'flot-tooltip'
			}
		});
	}
	
	if (jQuery('#countryChart').length) {
		jQuery.plot('#countryChart',countryChart,{
			series: {
				pie: {
					show: true,
					stroke: {
						width: 2,
					},
				},
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			tooltip: true,
			tooltipOpts: {
				content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
				shifts: {
					x: 20,
					y: 0
				},
				defaultTheme: false,
				cssClass: 'flot-tooltip'
			}
		});
	}
	
	if (jQuery('#mobileDeviceChart').length) {
		jQuery.plot('#mobileDeviceChart',mobileDeviceChart,{
			series: {
				pie: {
					show: true,
					stroke: {
						width: 2,
					},
				},
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			tooltip: true,
			tooltipOpts: {
				content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
				shifts: {
					x: 20,
					y: 0
				},
				defaultTheme: false,
				cssClass: 'flot-tooltip'
			}
		});
	}
	
	if (jQuery('#deviceCategoryChart').length) {
		jQuery.plot('#deviceCategoryChart',deviceCategoryChart,{
			series: {
				pie: {
					show: true,
					stroke: {
						width: 2,
					},
				},
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			tooltip: true,
			tooltipOpts: {
				content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
				shifts: {
					x: 20,
					y: 0
				},
				defaultTheme: false,
				cssClass: 'flot-tooltip'
			}
		});
	}
});