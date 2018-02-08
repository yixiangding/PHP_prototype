<?php 
	$input_empty = true;
	if (isset($_POST['input_text']) && trim($_POST['input_text'] == false)) {

	} else {
		$input_empty = false;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Stock Search</title>

	<style type="text/css">
		#search_form {
			background-color: rgb(245,245,245);
			width: 400px;
			margin: auto;
			height: 160px;
			padding: 10px;
			box-sizing: border-box;
		}
		#form_title {
			font-weight: normal;
			font-size: 30px;
			text-align: center;
		}
		#division_line {
			background-color: rgb(223,223,223);
			width: 100%;
			height: 1px;
			margin: auto;
			margin-bottom: 15px;
		}
		.inline_div {
			display: inline-block;
		}
		#instruction {
			float: left;
		}
		#data_chart,#news_chart {
			width: 100%;
			max-width: 1200px;
			min-width: 800px;
			margin: 10px auto;
			border-collapse: collapse;
			border: 1px solid rgb(217,217,217);
			font-size: 12px;
		}
		#data_chart th,#news_chart th {
			width: 30%;
			text-align: left;
			background-color: rgb(245,245,245);
			border: 1px solid rgb(217,217,217);
		}
		#data_chart td,#news_chart td {
			width: 70%;
			text-align: center;			
			background-color: rgb(251,251,251);
			border: 1px solid rgb(217,217,217);
		}
		.arrows {
			height: 12px;
		}
		.indicator_link {
			color: rgb(0, 24, 197);
			margin: 0 10px;
			text-decoration: none;
		}
		.indicator_link:hover {
			color: rgb(63,62,65);
		}
		#container {
			width: 100%;
			height: 600px;
			max-width: 1200px;
			min-width: 800px;
			margin: 10px auto;
		}
		#source, .news_link {
			text-decoration: none;
		}
		#source:hover {
			color: rgb(63, 62, 65);
		}
		.news_link:hover {
			color: rgb(63, 62, 65);
		}
		#news_control {
			color: rgb(191, 191, 191);
			font-size: 10px;
			text-align: center;
			margin: auto;
			width: 150px;
		}
		#news_arrow {
			width: 25px;
			margin: 5px;
		}
		#news_chart td {
			text-align: left;
		}
	</style>
	

	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>

	<script>
		var chart;
		var collapsed = true;

		function clearText() {
			document.getElementById('input_text').value = "";
			if(document.getElementById('big_wrapper') != null) document.getElementById('big_wrapper').style.display = 'none';
		}

		function news_control() {
			var control_text = document.getElementById("click_show_collapse");
			var control_arrow = document.getElementById("news_arrow");
			var news_content = document.getElementById("news_wrapper");
			if (collapsed) {
				// show news
				collapsed = false;
				control_text.innerHTML = "click to hide stock news";
				control_arrow.src = "http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Up.png";
				news_content.style.display = "block";
			} else {
				// collapse news
				collapsed = true;
				control_text.innerHTML = "click to show stock news";
				control_arrow.src = "http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png";
				news_content.style.display = "none";
			}
		}

		function submit_form() {
			if (document.getElementById('input_text').value.trim() == "") {
				alert("Please enter a symbol");
				return false;
			} else return true;
		}

		function request_single(request_symbol) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
       			var response = JSON.parse(xmlhttp.responseText);
       			var SMA_value = response["Technical Analysis: " + request_symbol];
       			var title = response["Meta Data"]["2: Indicator"];
       			var SMA_temp = [];
       			var count = 0;
       			for (var key in SMA_value) {
       				SMA_temp.unshift(parseFloat(SMA_value[key][request_symbol]));
       				count++;
       				if (count >= 121) break;
       			}

       			// make SMA chart
				var option = {
			        chart: {
			            zoomType: 'x',
			            borderColor: '#ccc',
    					borderWidth: 1
			        },
			        title: {
			            text: title
			        },
			        subtitle: {
			        	useHTML: true,
			        	text: '<a id="source" href="https://www.alphavantage.co/">Source: Alpha Vantage</a>',
			        	style: {
			        		color: 'rgb(54, 61, 206)'
			        	}
			        },
			        xAxis: {
			            type: 'category',
			            categories: cate,
			            tickInterval: 5,
			        	labels: {
			        		formatter: function () {
			        			return this.value.substring(5,7) + '/' + this.value.substring(8);
			        		},
			        		style: {
			        			fontSize: '6px'
			        		}
			        	}
			        },
			        yAxis: {
			        	labels: {
			        		format: '{value}'
			        	},
			            title: {
			                text: request_symbol
			            },
			            tickInterval: null,
			            max: null,
			            style: {
			        		fontSize: '7px'
			        	}
			        },
			        legend: {
			            layout: 'vertical',
			            align: 'right',
			            verticalAlign: 'middle',
			            borderWidth: 0
			        },
			        tooltip: {
			        	formatter: function() {
			        		var tip = this.x.substring(5,7) + '/' + this.x.substring(8);
			        		tip += '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': ' + Highcharts.numberFormat(this.y, 2);
			        		return tip;
			        	}
			        },
			        plotOptions: {
				        series: {
				            marker: {
				                enabled: true,
				                radius: 1.5,
				                symbol: 'square'
				            }
				        },
				        spline: {
				        	lineWidth: 0.3
				        }
				    },
			        series: [{
			            type: 'spline',
			            name: symbol,
			            data: SMA_temp,
			            color: 'rgb(202, 56, 39)',
			        }],
			    };
			    chart = Highcharts.chart('container', option);
			}
		};
			xmlhttp.open("GET", 'https://www.alphavantage.co/query?function=' + request_symbol + '&symbol=' + symbol + '&interval=daily&time_period=10&series_type=close&apikey=QTAX2CXFZ8AQE95Z', true);
			xmlhttp.send();
		}

		function request_double(request_symbol) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
	       			var response = JSON.parse(xmlhttp.responseText);
	       			var STOCH_value = response["Technical Analysis: " + request_symbol];
	       			var title = response["Meta Data"]["2: Indicator"];
	       			var SlowK_temp = [];
	       			var SlowD_temp = [];
	       			var count = 0;
	       			for (var key in STOCH_value) {
	       				SlowK_temp.unshift(parseFloat(STOCH_value[key]["SlowK"]));
	       				SlowD_temp.unshift( parseFloat(STOCH_value[key]["SlowD"]));
	       				count++;
	       				if (count >= 121) break;
	       			}

					var option = {
				        chart: {
				            zoomType: 'x',
				            borderColor: '#ccc',
				    		borderWidth: 1,
				        },
				        title: {
				            text: title
				        },
				        subtitle: {
				        	useHTML: true,
				        	text: '<a id="source" href="https://www.alphavantage.co/">Source: Alpha Vantage</a>',
				        	style: {
				        		color: 'rgb(54, 61, 206)'
				        	}
				        },
				        xAxis: {
				            type: 'category',
				            categories: cate,
				            tickInterval: 5,
				        	labels: {
				        		formatter: function () {
				        			return formatDate(this.value);
				        		},
				        		style: {
				        			fontSize: '6px'
				        		}
				        	},
				        	crosshair: true
				        },
				        yAxis: {
				        	labels: {
				        		format: '{value}'
				        	},
				            title: {
				                text: request_symbol
				            },
				            tickInterval: null,
				            max: null,
				            style: {
			        			fontSize: '6px'
				        	}
				        },
				        plotOptions: {
				            series: {
					            marker: {
					                enabled: true,
					                radius: 1.5,
					                symbol: 'square'
					            }
				       		},
					        spline: {
					        	lineWidth: 0.5
					        }
				        },
				        legend: {
				            layout: 'vertical',
				            align: 'right',
				            verticalAlign: 'middle',
				            borderWidth: 0
				        },

				        tooltip: {
				        	formatter: function() {
				        		var points = this.points; // array[# of series]
				        		var tip = this.x.substring(5,7) + '/' + this.x.substring(8);
				        		for (var i in points) {
					        		tip += '<br/><span style="color:' + points[i].color + '">\u25CF</span> ' + points[i].series.name + ': ' + Highcharts.numberFormat(points[i].y);
				        		}
				        		return tip;
				        	},
				        	shared: true
				        },

				        series: [{
				            type: 'spline',
				            name: symbol + " SlowK",
				            data: SlowK_temp,
				            color: 'rgb(184, 44, 11)',
				        },
				        {
				        	type: 'spline',
				        	name: symbol + " SlowD",
				        	data: SlowD_temp,
				        	color: 'rgb(152, 193, 233)',
				        }]
				    };
				    var chart = Highcharts.chart('container', option);	       								
				}
			}
			xmlhttp.open("GET", 'https://www.alphavantage.co/query?function=' + request_symbol + '&symbol=' + symbol + '&interval=daily&time_period=10&series_type=close&apikey=QTAX2CXFZ8AQE95Z', true);
			xmlhttp.send();			
		}

		function request_treble(request_symbol, name1, name2, name3) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
	       			var response = JSON.parse(xmlhttp.responseText);
	       			var response_value = response["Technical Analysis: " + request_symbol];
	       			var title = response["Meta Data"]["2: Indicator"];
	       			var RMB_temp = [];
	       			var RUB_temp = [];
	       			var RLB_temp = [];
	       			var count = 0;
	       			for (var key in response_value) {
	       				RMB_temp.unshift(parseFloat(response_value[key][name1]));
	       				RUB_temp.unshift(parseFloat(response_value[key][name2]));
	       				RLB_temp.unshift(parseFloat(response_value[key][name3]));
	       				count++;
	       				if (count >= 121) break;
	       			}

					var option = {
				        chart: {
				            zoomType: 'x',
				            borderColor: '#ccc',
				    		borderWidth: 1,
				        },
				        title: {
				            text: title
				        },
				        subtitle: {
				        	useHTML: true,
				        	text: '<a id="source" href="https://www.alphavantage.co/">Source: Alpha Vantage</a>',
				        	style: {
				        		color: 'rgb(54, 61, 206)'
				        	}
				        },
				        xAxis: {
				            type: 'category',
				            categories: cate,
				            tickInterval: 5,
				        	labels: {
				        		formatter: function () {
				        			return this.value.substring(5,7) + '/' + this.value.substring(8);
				        		},
				        		style: {
				        			fontSize: '6px'
				        		}
				        	},
				        	crosshair: true
				        },
				        yAxis: {
				        	labels: {
				        		format: '{value}'
				        	},
				            title: {
				                text: request_symbol
				            },
				            tickInterval: null,
				            max: null,
				            style: {
			        			fontSize: '6px'
				        	}
				        },
				        plotOptions: {
				            series: {
					            marker: {
					                enabled: true,
					                radius: 1.5,
					                symbol: 'square'
					            }
				       		},
					        spline: {
					        	lineWidth: 0.5
					        }
				        },
				        legend: {
				            layout: 'vertical',
				            align: 'right',
				            verticalAlign: 'middle',
				            borderWidth: 0
				        },

				        tooltip: {
				        	formatter: function() {
				        		var points = this.points; // array[# of series]
				        		var tip = this.x.substring(5,7) + '/' + this.x.substring(8);
				        		for (var i in points) {
					        		tip += '<br/><span style="color:' + points[i].color + '">\u25CF</span> ' + points[i].series.name + ': ' + Highcharts.numberFormat(points[i].y);
				        		}
				        		return tip;
				        	},
				        	shared: true
				        },

				        series: [{
				            type: 'spline',
				            name: symbol + " " + name1,
				            data: RMB_temp,
				            color: 'rgb(180, 50, 24)',
				        },
				        {
				        	type: 'spline',
				        	name: symbol + " " + name2,
				        	data: RUB_temp,
				        	color: 'rgb(84, 84, 86)',
				        },
				        {
				        	type: 'spline',
				        	name: symbol + " " + name3,
				        	data: RLB_temp,
				        	color: 'rgb(180, 230, 162)',
				        }]
				    };
				    var chart = Highcharts.chart('container', option);	       								
				}
			}
			xmlhttp.open("GET", 'https://www.alphavantage.co/query?function=' + request_symbol + '&symbol=' + symbol + '&interval=daily&time_period=10&series_type=close&apikey=QTAX2CXFZ8AQE95Z', true);
			xmlhttp.send();			
		}
	</script>
</head>
<body>
<form id="search_form" action="stock.php" method="post">
	<div id="form_title"><i>Stock Search</i></div>
	<div id="division_line"></div>
	<div class="inline_div" id="instruction">Enter Stock Ticker Symbol:*</div>
	<div class="inline_div">
		<input id="input_text" type="text" name="input_text" value="<?php echo isset($_POST['input_text']) ? $_POST['input_text'] : ""; ?>">
		<div id="btns">
			<input id="search_btn" type="submit" name="submit" value="Search" onclick="return submit_form();">
			<input id="clear_btn" type="button" name="Clear" value="Clear" onclick="clearText()">
		</div>
	</div>
	<div><i>* - Mandatroy fields</i></div>
</form>



<?php 
	if (!isset($_POST['submit'])) {

	} else if (!$input_empty) {
		$input_symbol = $_POST['input_text'];
		$query_url = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=' . rawurlencode($input_symbol) . '&outputsize=full&apikey=QTAX2CXFZ8AQE95Z';
		date_default_timezone_set('US/Eastern');
		$arrContextOptions=array(
				"ssl" => array(
	    		"verify_peer"=> false,
	    		"verify_peer_name"=> false,
			),
		);
		$JSON_content = @file_get_contents($query_url, false, stream_context_create($arrContextOptions));
		$content = json_decode($JSON_content);
		$input_valid = false;
		if (!isset($content->{"Error Message"})) $input_valid = true;

		if (!$input_valid) {
			echo "<div id=\"big_wrapper\"><table id=\"data_chart\"><tr><th>Error</th><td>Error: NO record has been found, please enter a valid symbol</td></tr></table></div>";
		} else { // input is valid
?>


<?php
	if (false === $JSON_content) {
		// error while fetching
		echo "<script>alert('Fail to connect with server! Please refresh and try again!');</script>";
		exit();
	} else {
		// process necessary data
		$time_series = $content->{"Time Series (Daily)"};
		$cur_close = reset($time_series)->{"4. close"};
		$cur_open = reset($time_series)->{"1. open"};
		$prev_close = next($time_series)->{"4. close"};
		$change_value = round($cur_close - $prev_close, 2);
		$change_percent = round(round($cur_close - $prev_close, 2) * 100 / $prev_close, 2);
		$volume_value = reset($time_series)->{"5. volume"};
		$volume_with_comma = "" . $volume_value;
		for ($i = strlen($volume_with_comma) - 4; $i >= 0; $i -= 3) {
			$volume_with_comma = substr($volume_with_comma, 0, $i + 1) . "," . substr($volume_with_comma, $i + 1);
		}
		reset($time_series);
		$timestamp = key($time_series);

		// parsing JSON to JavaScript
		$chart_data = array();
		$volume_data = array();
		$pointer = reset($time_series);
		// date_default_timezone_set('UTC');
		$count = 0;
		while ($pointer && $count < 121) {
			// push [time, price] into $chart_data
			array_unshift($chart_data, array(key($time_series), (float) $pointer->{"4. close"})); // strtotime: sec --> Date: msec
			array_unshift($volume_data, array(key($time_series), (float) $pointer->{"5. volume"}));
			$pointer = next($time_series);
			$count++;
		}
		echo '<script>var price_data = ' . json_encode($chart_data) . ';</script>';
		echo '<script>var volume_data = ' . json_encode($volume_data) . ';</script>';
		echo '<script>var symbol = "' . $input_symbol . '";</script>';
		echo '<script>var cur_date = "' . date_format(new DateTime($timestamp), 'm/d/Y') . '";</script>';

		// set up Indicators in table
		$Price_link = "<a class=\"indicator_link\" href=\"javascript:void(0)\" onclick=\"price_chart()\">Price</a>  ";
		$SMA_link = "<a class=\"indicator_link\" onclick=\"request_single('SMA')\" href=\"javascript:void(0)\">SMA</a>  ";
		$EMA_link = "<a class=\"indicator_link\" onclick=\"request_single('EMA')\" href=\"javascript:void(0)\">EMA</a>  ";
		$STOCH_link = "<a class=\"indicator_link\" onclick=\"request_double('STOCH')\" href=\"javascript:void(0)\">STOCH</a>  ";
		$RSI_link = "<a class=\"indicator_link\" onclick=\"request_single('RSI')\" href=\"javascript:void(0)\">RSI</a>  ";
		$ADX_link = "<a class=\"indicator_link\" onclick=\"request_single('ADX')\" href=\"javascript:void(0)\">ADX</a>  ";
		$CCI_link = "<a class=\"indicator_link\" onclick=\"request_single('CCI')\" href=\"javascript:void(0)\">CCI</a>  ";
		$BBANDS_link = "<a class=\"indicator_link\" onclick=\"request_treble('BBANDS', 'Real Middle Band', 'Real Upper Band', 'Real Lower Band')\" href=\"javascript:void(0)\">BBANDS</a>  ";
		$MACD_link = "<a class=\"indicator_link\" onclick=\"request_treble('MACD', 'MACD', 'MACD_Hist', 'MACD_Signal')\" href=\"javascript:void(0)\">MACD</a>";

		// arrow img for chart
		if ($change_value > 0) {
			$change_icon = "<img class=\"arrows\" src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\">";
		} else {
			$change_icon = "<img class=\"arrows\" src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\">";
		}

		// build the chart
		$data_chart = "<div id='big_wrapper'><table id='data_chart'>
						<tr>
							<th>Stock Ticker Symbol</th>
							<td>" . $content->{"Meta Data"}->{"2. Symbol"} . "</td>
						</tr>
						<tr>
							<th>Close</th>
							<td>" . $cur_close . "</td>
						</tr>
						<tr>
							<th>Open</th>
							<td>" . $cur_open . "</td>
						</tr>
						<tr>
							<th>Previous Close</th>
							<td>" . $prev_close . "</td>
						</tr>
						<tr>
							<th>Change</th>
							<td>" . $change_value . $change_icon . "</td>
						</tr>
						<tr>
							<th>Change Percent</th>
							<td>" . $change_percent . "%" . $change_icon . "</td>
						</tr>
						<tr>
							<th>Day's Range</th>
							<td>" . round(reset($time_series)->{"3. low"}, 2) . " - " . round(current($time_series)->{"2. high"}, 2) . "</td>
						</tr>
						<tr>
							<th>Volume</th>
							<td>" . $volume_with_comma . "</td>
						</tr>
						<tr>
							<th>Timestamp</th>
							<td>" . $timestamp . "</td>
						</tr>
						<tr>
							<th>Indicators</th>
							<td>" . $Price_link . $SMA_link . $EMA_link . $STOCH_link . $RSI_link . $ADX_link . $CCI_link . $BBANDS_link . $MACD_link . "</td>
						</tr>
					</table>";
	}
	echo $data_chart;
?>

<div id="container"></div>
<div id="news_control" onclick="news_control()">
	<div id="click_show_collapse">click to show stock news</div>
	<img id="news_arrow" src="http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png">
</div>



<script>
	// make chart
	function formatDate(value) {
		var temp = new Date(value);
		temp = (temp.getUTCMonth() + 1) + '/' + temp.getUTCDate();
		return temp;
	}

	var cate = [];
	var price = [];
	var volume = [];
	for (var key in price_data) {
		var value = price_data[key];
		cate.push(value[0]);
		price.push(value[1]);
		volume.push(volume_data[key][1]);
	}
	price_chart();

	function price_chart() {
		var option = {
	        chart: {
	            zoomType: 'x',
	            borderColor: '#ccc',
	    		borderWidth: 1,
	        },
	        title: {
	            text: 'Stock Price (' + cur_date + ')'
	        },
	        subtitle: {
	        	useHTML: true,
	        	text: '<a id="source" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>',
	        	style: {
	        		color: 'rgb(54, 61, 206)'
	        	}
	        },
	        xAxis: {
	            type: 'category',
	            categories: cate,
	            tickInterval: 5,
	        	labels: {
	        		formatter: function () {
	        			return this.value.substring(5,7) + '/' + this.value.substring(8);
	        		},
	        		style: {
	        			fontSize: '6px'
	        		}
	        	}
	        },
	        yAxis: [{
	        	labels: {
	        		format: '{value}'
	        	},
	            title: {
	                text: 'Stock Price'
	            },
	            tickInterval: 5
	        },
	        {
	        	labels: {
	        		formatter: function () {
	        			return this.value / 1000000 + 'M';
	        		}
	        	},
	            title: {
	            	text: 'Volume'
	            },
	            tickInterval: 80000000,
	            max: 300000000,
	            opposite: true
	        }],
	        plotOptions: {
	            area: {
	                fillColor: 'rgba(231, 143, 142, 0.75)',
	                lineColor: 'rgba(192, 53, 54, 0.9)',
	                lineWidth: 1,
	                states: {
	                    hover: {
	                        lineWidth: 1
	                    }
	                },
	                threshold: null
	            },
	            column: {
	            	color: 'rgb(255, 255, 255)',
	            	groupPadding: 0.1,
	            	pointWidth: 0.2
	            },
	            series: {
	            	marker: {
	            		enabled: false,
	            	}
	            }
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'middle',
	            borderWidth: 0
	        },

	        tooltip: {
	        	formatter: function() {
	        		var tip = this.x.substring(5,7) + '/' + this.x.substring(8);
	        		tip += '<br/><span style="color:' + this.color + '">\u25CF</span> ' + this.series.name + ': ' + this.y;
	        		return tip;
	        	}
	        },

	        series: [{
	            type: 'area',
	            name: symbol,
	            data: price,
	            color: 'rgba(231, 143, 142, 0.80)',
	        },
	        {
	        	type: 'column',
	        	name: symbol + " Volume",
	        	data: volume,
	        	yAxis: 1
	        }]
	    };
	    var chart = Highcharts.chart('container', option);
	}
</script>

<?php
// get news content
$news_content = @file_get_contents("http://seekingalpha.com/api/sa/combined/" . $input_symbol . ".xml");
if ($news_content != false) { // judge $news_content


$news_xml = simplexml_load_string($news_content);
$news_xml = $news_xml->channel->item;
echo "<div id=\"news_wrapper\" style=\"display: none;\">";
echo "<table id=\"news_chart\">";
$count = 0;
$news_items = array();
foreach ($news_xml as $item) {
	if (strpos($item->{'guid'}, 'Article') == false) continue;
	array_push($news_items, $item);
	$count++;
	if ($count == 5) break;
}
echo "</table>";
echo "</div>";
$news_items = json_encode($news_items);
echo "<script>var news_items = " . $news_items . ";</script>";
?>

<script>
	var news_chart = document.getElementById('news_chart');
	var news_inner = "";
	for (var i in news_items) { // news_items: array contains 5 news
		var news_title = news_items[i].title;
		// var news_link = "https://seekingalpha.com/news/" + news_items[i].guid.substring(-7);
		var news_link = news_items[i].link;
		var news_date = news_items[i].pubDate.substring(0, news_items[i].pubDate.length - 6);
		news_inner += "<tr><td><a class='news_link' href=" + news_link + " target='_blank'>" + news_title + "</a> &nbsp &nbsp &nbsp " + "Publicated Time: " + news_date + "</td></tr>";
	}
	news_chart.innerHTML = news_inner + '</div>';
</script>
<!-- API Key: QTAX2CXFZ8AQE95Z -->
</body>
</html>

<?php 
		}	// judge $news_content
	}
}
?>