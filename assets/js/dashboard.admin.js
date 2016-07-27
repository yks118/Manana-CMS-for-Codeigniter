jQuery(function(){
	if (jQuery('#visitorChart').length) {
		var visitorData = new Array();
		
		visitorData.push({
			data:lastWeek,
			label:'Last Week',
			bars:{
				show:true,
				barWidth:0.08,
				order:1,
				lineWidth:0,
				fillColor:'#f44336'
			}
		});
		
		visitorData.push({
			data:nowWeek,
			label:'This Week',
			bars:{
				show:true,
				barWidth:0.08,
				order:2,
				lineWidth:0,
				fillColor:'#03a9f4'
			}
		});
		
		jQuery.plot(jQuery('#visitorChart'),visitorData,{
			grid:{
				borderWidth:1,
				borderColor:'#eee',
				show:true,
				hoverable:true,
				clickable:true
			},
			yaxis:{
				tickColor:'#eee',
				tickDecimals:0,
				font:{
					lineHeight:13,
					style:'normal',
					color:'#9f9f9f',
				},
				shadowSize:0
			},
			xaxis:{
				tickColor:'#fff',
				tickDecimals:0,
				font:{
					lineHeight:13,
					style:'normal',
					color:'#9f9f9f'
				},
				shadowSize:0,
				mode:'categories'
			},
			legend:{
				container:'.flc-bar'
			},
			tooltip: true,
			tooltipOpts: {
				content: "%y, %s",
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
