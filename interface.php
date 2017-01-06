<html>
	<head>
		<title>Survey Engine</title>
		<style type="text/css">
			body {
				font-family:sans-serif;
				padding:2em;
			}
			form { 
				display:inline;
			}
			
			label {
				display:block;
				margin-top:1em;
			}
			caption {
				font-size:1.2em;
				font-weight:bold;
				text-align:left;
				margin-top:1em;
			}
			
			th {
				text-align:left;
				white-space:nowrap;
			}
			input {
				margin-right:1em;
			}
		</style>
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<script>
			var vrndtxt = '', vrndval = 0;
			function rndtxt() {
				if (vrndtxt) return vrndtxt;
				else return (0|Math.random()*9e6).toString(36);
			}
			function rndval(max) {
				if (!max) max = 32;
				if (vrndval) return vrndval%max;
				else return Math.floor(Math.random()*max);
			}
			
			function randomize(form,medium) {
				
				if (medium) {
					vrndtxt = '';
					vrndval = 0;
					vrndtxt = rndtxt();
					vrndval = rndval(32);
				} else {
					vrndtxt = '';
					vrndval = 0;
				}
				
				$('input[type="text"]',form).each(function() {
					$(this).val(rndtxt());
				});
				$('select',form).each(function() {
					var max = $('option',this).size();
					$('option',this).get(rndval(max)).selected=true;
				});
				$('input[type="radio"]',form).data('grouped',false);
				$('input[type="radio"]',form).each(function() {
					if (!$(this).data('grouped')) {
						var $group = $('input[type="radio"][name="'+$(this).attr('name')+'"]',form);
						var max = $group.size();
						$group.get(rndval(max)).checked=true;
						$group.data('grouped',true);
					}
				});
			}
		</script>
	</head>
	<body>
		<h1>Survey Engine</h1>
		
		<form action="survey.php">
			<input type="hidden" name="action" value="results">
			<input type="hidden" name="format" value="html">
			<input type="submit" value="results (html)">
		</form>
		<form action="survey.php" target="results">
			<input type="hidden" name="action" value="results">
			<input type="hidden" name="format" value="csv">
			<input type="submit" value="results (csv)">
		</form>
		<form action="survey.php" target="results">
			<input type="hidden" name="action" value="results">
			<input type="hidden" name="format" value="json">
			<input type="submit" value="results (json)">
		</form>
		<form action="survey.php">
			<input type="hidden" name="action" value="form">
			<input type="hidden" name="format" value="html">
			<input type="submit" value="form">
		</form>
		<form action="survey.php">
			<input type="hidden" name="action" value="clear">
			<input type="hidden" name="format" value="html">
			<input type="submit" value="clear database ..">
		</form>
		<div>
			<?php
				
				if (!$response['result']['success']) {
					print '<h3>Error '.$response['result']['code'].'</h3>';
				} 
				
				if (count($response['result']['messages'])) {
					print '<ul id="messages"><li>'.implode('</li><li>',$response['result']['messages']).'</li></ul>';
				}
				
				if ($response['result']['success']) {
					print $response['result']['html'];
				}
			?>
		</div>
		
	</body>
</html>