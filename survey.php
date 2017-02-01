<?php

	/*
	
		POST
		
			// setup --------------------------
			action=submit&
			format=json&
			bogus=1&
			
			// field:value ------------------------
			email=2qpto&
			
			// cat:opt -------------------------
			age=5&
			gender=&
			education=&
			residence=1&
			province=12&
			
			// statement:position ---------------
			s2390=-1& 
			s2391=-1&
			s2392=1&
			s2393=1&
			s2394=1&
			s2395=0
			
		$config (from config.php)
		
		$survey (read from survey.json)
		
			fields
				foo
					column
					label
					
			categories
				foo
					column
					label
					options
						value:label
						
			statements
				foo
					column
					label
					
			positions
				value:label

			results (read from db)
				categories
					foo
						bar
							total:val
						total:val
				statements
					quz
						categories
							foo
								bar
									positions
										pos:total
										pos:total
										pos:total
									total:val
				total:val
										
			
			
		$request
			action	test,form,clear,submit,results,default
			format	json,csv,html
			bogus		true:false
			ip			remote ip (for submit)
			now			time()
			pass		for admin
			$key		$value (for survey input)
			
		$result
			success		true:false
			code			errcode
			messages	[]
			survey		(for json object)
			csv				(csv string)
			html			html string
			bogus			true:false
			id				returned from insert
			
		flow:
			main()
				if (success) 
					success()
						respond()
							opt include interface
				else 
					failure()
						respond()
							opt include interface
					
	*/
	
	
	// read config 
	if (!isset($config)) {
		require('config.php');
	}
	
	// parse request
	$request = array();
	$request['action'] 	= isset($_REQUEST['action']) 	? $_REQUEST['action'] : 'results';
	$request['format'] 	= isset($_REQUEST['format'])	? $_REQUEST['format'] : 'html';
	$request['bogus'] 	= isset($_REQUEST['bogus']) 	? $_REQUEST['bogus'] : $config->testing;
	
	$request['filtercat']	= $config->filtercat;
	$request['filterval']	= $config->filterval;
	
	// read survey
	$survey = file_get_contents('survey.json');
	
	
	
	// in case you have encoding issues, try
	// print  mb_detect_encoding($survey, 'UTF-8, ISO-8859-1', true);
	//$survey = utf8_encode($survey); 
	//$survey = utf8_decode($survey); 
	//$survey = mb_convert_encoding($survey, 'HTML-ENTITIES', "UTF-8");
	//$survey = mb_convert_encoding($survey, 'UTF-8', mb_detect_encoding($survey, 'UTF-8, ISO-8859-1', true));
          
	$survey = json_decode($survey,true); 
	if (!$survey) {
		failure(2,"Failed to read survey.json ".json_error());
	}
	
	// blank result
	$result = array(
		'success'	=> false,
		'messages'	=> array()
	);
	
	
	// connect
	$mysqli = new mysqli("localhost", $config->mysqluser, $config->mysqlpass, $config->mysqldb);
	if ($mysqli->connect_errno) {
    	failure(1,"Failed to connect to MySQL: " . $mysqli->connect_error);
	}
	
	// find filtercol if any
	if ($request['filtercat']) {
		$request['filtercol']	= $survey['categories'][$request['filtercat']]['column'];
		if (!$request['filtercol']) {
			failure(6,"Can not find filter column for " . $request['filtercat']);
		}
	}
	
	main();
	
	function main() {
		
		global $config,$request,$result,$survey,$mysqli;
		
		switch($request['action']) {
		
			case "test" :
				require_login();
				$result['survey']=&$survey;
				success();
				break;
				
			case "form" :
				require_login();
				$html = '<form action="'.$_SERVER['PHP_SELF'].'" method="get">';
				$html .= '<input type="hidden" name="action" value="submit">';
				$html .= '<input type="hidden" name="format" value="html">';
				
				$html .= '<label>Randomize input</label>';
				$html .= '<input type="button" onclick="randomize(this.form,true)" value="medium random">';
				$html .= '<input type="button" onclick="randomize(this.form)" value="total random">';
				
				if ($request['bogus']) {
					$html .= '<input type="hidden" name="bogus" value="1">';
				}
				
				if (count((array)$survey['fields'])) {
					$html .= '<h3>Fields</h3>';
					foreach($survey['fields'] as $key=>$field) {
						$html .= '<label>'.$field['label'].'</label>';
						$html .= '<input type="text" name="'.$key.'">';
					}
				}
				
				if (count((array)$survey['categories'])) {
					$html .= '<h3>Categories</h3>';
					foreach($survey['categories'] as $key=>$cat) {
						if ($key!=$request['filtercat']) {
							$html .= '<label>'.$cat['label'].'</label>';
							$html .= '<select name="'.$key.'">';
							$html.= '<option value="">Select...</option>';
							foreach($cat['options'] as $val=>$option) {
								$html.= '<option value="'.$val.'">'.$option.'</option>';
							}
							$html .= '</select>';
						} else {
						//	$html .= '<label>'.$cat['label'].'</label>';
						//	$html .= '<select name="'.$key.'">';
						//	$html.= '<option value="'.$request['filterval'].'">'.$cat['options']->{$request['filterval']}.'</option>';
						//	$html .= '</select>';
						}
					}
				}
				
				if (count((array)$survey['statements'])) {
					$html .= '<h3>Statements</h3>';
					foreach($survey['statements'] as $key=>$stat) {
						$html .= '<label>'.$stat['label'].'</label>';
						foreach($survey['positions'] as $val=>$pos) {
							$html.= '<input type="radio" name="'.$key.'" value="'.$val.'">'.$pos.'</option>';
						}
						
					}
				}
				$html .= '<br><br><input type="submit" value="submit">';
				$html .= '</form>';
				
				$result['html']=$html;
				success();
				break;
				
			case "clear" :
			
				require_login();
				if (isset($_REQUEST['admuips']) && $_REQUEST['admuips'] == $config->adminpass) {
		
					// check if this ip has recently submitted .. 
					$query = 'DELETE FROM survey ';
					if ($request['filtercat']) $query .= ' WHERE '.$request['filtercol'].'='.$request['filterval'];
					
					$res = $mysqli->query($query) or die($mysqli->error);
					
					$result['messages'][] = 'Database cleared.';
					success();
				} else {
					if (isset($_REQUEST['admuips'])) {
						$result['messages'][] = 'Wrong pass';
					}
					if ($request['format']=='html') {
						$result['html'] = '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
						$result['html'] .= '<input type="hidden" name="action" value="clear">';
						$result['html'] .= '<b>Doing this will permanently delete all submissions!</b>';
						$result['html'] .= '<label>admin password:</label>';
						$result['html'] .= '<input type="password" name="admuips" value="">';
						$result['html'] .= '<input type="submit"  value="Clear database">';
						success();
					} else {
						$result['messages'] = 'Clearing the db requires a password';
						failure();
					}
				
				}
				break;
				
			case "submit" :
			
				$request['ip']		= md5(getClientIP());
				$request['now']		= time();
		
		
				// check if this ip has recently submitted .. 
				if (!$request['bogus']) {
					$query = 'SELECT count(id) FROM survey ';
					$query .= 'WHERE ip = "'.$request['ip'].'" ';
					$query .= 'AND cdate >= NOW() - INTERVAL 1 HOUR ';
					$query .= 'AND NOT(bogus)';
					$res = $mysqli->query($query) or die($mysqli->error);
					$row = $res->fetch_row();
					if ($row[0]>0) {
						failure(3,"Too many submissions: " . $row[0]);
					}
				}
				
				// let them know
				if ($request['bogus']) $result['bogus']=true;
				
				// force filter entry
				if ($request['filtercat']) {
					$_REQUEST[$request['filtercat']]=$request['filterval'];
				}
				
				// submit values
				$cols  = array('ip','bogus');
				$vals = array($request['ip'],$request['bogus']);
				foreach($survey['fields'] as $key=>$field) {
					if (isset($_REQUEST[$key])) {
						if ($_REQUEST[$key]!=='') {
							$request[$key] = $_REQUEST[$key];
							array_push($cols,$field['column']);
							array_push($vals,$request[$key]);
						} else {
							$result['messages'][] = 'Ignoring empty field '.$key;
						}
					}
				}
				foreach($survey['categories'] as $key=>$cat) {
					if (isset($_REQUEST[$key])) {
						if ($_REQUEST[$key]!=='') {
							$request[$key] = $_REQUEST[$key];
							array_push($cols,$cat['column']);
							array_push($vals,$request[$key]);
						} else {
							$result['messages'][] = 'Ignoring empty cat '.$key;
						}
					}
				}
				foreach($survey['statements'] as $key=>$stat) {
					if (isset($_REQUEST[$key])) {
						if ($_REQUEST[$key]!=='') {
							$request[$key] = $_REQUEST[$key];
							array_push($cols,$stat['column']);
							array_push($vals,$request[$key]);
						} else {
							$result['messages'][] = 'Ignoring empty statement '.$key;
						}
					}
				}
				
				$query = 'INSERT INTO survey ('.implode(',',$cols).') VALUES (';
				$query .= '"'.implode('","',$vals).'"';
				$query .= ');';
				//print $query;
				if ($res = $mysqli->query($query) or die($mysqli->error)) {
					$result['id']=$mysqli->insert_id;
					success();
				} else {
					failure(4,"Cant insert result: " . $mysqli->error);
				}
				break;
	
			case "results" :
				require_login();
				get_results();
				
				switch ($request['format']) {
					
					case 'json':
						$result['survey'] = &$survey;
						if ($request['filtercat']) {
							unset($survey['categories'][$request['filtercat']]);
							unset($survey['results']['categories'][$request['filtercat']]);
						}
						break;
						
					case 'csv':
						
						$csv = "";
						$numpos = count((array)$survey['positions']);
						
						$csv .= '"survey results dd '.date('Y/m/d').'"';
						$csv .= "\n\n";
							
						// loop categories
						foreach ($survey['categories'] as $ckey=>$cat) {
							
							if ($ckey != $request['filtercat']) {
								
								$csv .= '"Category: '.$cat['label'].' ['.$survey['results']['categories'][$ckey]['total'].']"';
								
								// header row for all cat opts
								$csv .= "\n";
									$csv .= '""';
									foreach ($cat['options'] as $val=>$label) {
										$csv .= ',"'.$label.'"';
										for ($cc=0;$cc<$numpos;$cc++) {
											$csv .= ',""';
										}
									}
									
								// header row cat stats
								$csv .= "\n";
									$cattotal = $survey['results']['categories'][$ckey]['total'];
									$csv .= '""';
									foreach ($cat['options'] as $val=>$label) {
										$catopttotal = $survey['results']['categories'][$ckey][$val]['total'];
										if ($cattotal) $catoptpct = round($catopttotal/$cattotal*100);
										else $catoptpct  =0;
										$csv .= ',"Total: '.$catopttotal.' ('.$catoptpct.'%)"';
										for ($cc=0;$cc<$numpos;$cc++) {
											$csv .= ',""';
										}
									}
		
								if (count((array)$survey['statements'])) {
									// header row for all positions
									$csv .= "\n";
										$csv .= '""';
										foreach ($cat['options'] as $val=>$opt) {
											//$csv .= ',"Total"';
											foreach ($survey['positions'] as $pkey=>$label) {
												$csv .= ',"'.$label.'"';
											}
											$csv .= ',""';
										}
									$csv .= "\n";
									
									// loop all statements
									foreach ($survey['results']["statements"] as $stkey=>$stres) {
										
										$csv .= '"'.$survey['statements'][$stkey]['label'].'"';
										foreach ($cat['options'] as $val=>$opt) {
											if ($stres['categories'][$ckey][$val]) {
												$total = $stres['categories'][$ckey][$val]['total'];
												//$csv .= ',"'.$total.'"';
												foreach ($survey['positions'] as $pkey=>$label) {
													$count = $stres['categories'][$ckey][$val]['positions'][$pkey];
													if ($count) $pct = round(100*$count/$total);
													else $pct = 0;
													$csv .= ',"'.$pct.'%"';
												}
												$csv .= ',""';
											}
										}
										$csv .= "\n";
									}
								}
								$csv .= "\n\n";
							} else {
							
							
								$csv .= '"Filtered by: '.$cat['label'];
								
								// header row for all cat opts
								$csv .= "\n";
									$csv .= '""';
									$csv .= $survey['categories'][$request['filtercat']]['label'].': ';
									$csv .= $survey['categories'][$request['filtercat']]['options'][$request['filterval']];
									$csv .= ' ['.$survey['results']['categories'][$request['filtercat']]['total'].']';
									$csv .= "\n\n";
							}
						}
						
						
						$result['csv']=$csv;
						
						break;
						
					default:
						
						//print '<xmp>';var_dump($survey['results']);print '</xmp>';
						
						$html = '<h2>survey results dd '.date('Y/m/d').' ['.$survey['results']['total'].']</h2>';
						$numpos = min(count((array)$survey['positions']),1);
						
						// loop categories
						foreach ($survey['categories'] as $ckey=>$cat) {
							
							if ($ckey != $request['filtercat']) {
							
								$html .= '<table border="1" width="100%">';
									$html .= '<caption>'.$cat['label'].' ['.$survey['results']['categories'][$ckey]['total'].']</caption>';
									
									// header row cat options
									$html .= '<tr>';
										$html .= '<th><!--stat--></th>';
										foreach ($cat['options'] as $val=>$label) {
											$html .= '<th colspan="'.($numpos).'">'.$label.'</th>';
										}
									$html .= '</tr>';
									
									// header row cat stats
									$html .= '<tr>';
										$cattotal = $survey['results']['categories'][$ckey]['total'];
										$html .= '<th>Total: '.$cattotal.'</th>';
										foreach ($cat['options'] as $val=>$label) {
											$catopttotal = $survey['results']['categories'][$ckey][$val]['total'];
											if ($cattotal) $catoptpct = round($catopttotal/$cattotal*100);
											else $catoptpct=0;
											$html .= '<th colspan="'.($numpos).'">'.$catopttotal.' ('.$catoptpct.'%)</th>';
										}
									$html .= '</tr>';
									
									// header row positions
									if (count((array)$survey['statements'])) {
										$html .= '<tr>';
											$html .= '<th><!--stat--></th>';
											foreach ($cat['options'] as $val=>$opt) {
												//$html .= '<th><small>Total</small></th>';
												foreach ($survey['positions'] as $pkey=>$label) {
													$html .= '<th title="'.$label.'"><small>'.substr($label,0,5).'..</small></th>';
												}
											}
										$html .= '</tr>';
										
										// row for each statement
										foreach ($survey['results']["statements"] as $stkey=>$stres) {
											$html .= '<tr>';
												$html .= '<th>'.$survey['statements']->{$stkey}['label'].'</th>';
												foreach ($cat['options'] as $val=>$opt) {
													if ($stres['categories'][$ckey][$val]) {
														$total = $stres['categories'][$ckey][$val]['total'];
														//$html .= '<td>'.$total.'</td>';
														foreach ($survey['positions'] as $pkey=>$label) {
															$count = $stres['categories'][$ckey][$val]['positions'][$pkey];
															if ($count) $pct = round(100*$count/$total);
															else $pct =0;
															$html .= '<td>'.$pct.'%</td>';
														}
													}
												}
											$html .= '</tr>';
										}
									}
								$html .= '</table>';
							} else {
						
								$html .= '<table border="1" width="100%">';
								$html .= '<caption> Filtered by ';
								$html .= $survey['categories'][$request['filtercat']]['label'].': ';
								// var_dump($survey['categories'][$request['filtercat']]);
								$html .= $survey['categories'][$request['filtercat']]['options'][$request['filterval']];
								$html .= ' ['.$survey['results']['categories'][$request['filtercat']]['total'].']</caption>';
								$html .= '</table>';
							}
						}  
						
						$result['html']=$html;
						
					
				}
				success();
				break;
	
			default:
				$result['messages'][]='no valid action given ('.$request['action'].')';
				success();
				
		}
	
	}
	
	function require_login() {
		global $config;
		
		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];
		foreach ($config->users as $uname=>$upass) {
			if ($user==$uname) {
				if ($pass == $upass) {
					return true;
				}
			}
		}
		
		// you got here
		header('WWW-Authenticate: Basic realm="Survey Engine"');
		header('HTTP/1.0 401 Unauthorized');
		die ("Not authorized");

	}
	
	function get_results() {
		global $config,$result,$request,$survey,$mysqli;
	
		// store results in the $survey.
		// prepare an array:
		/*
			"results"	: {
			
				"categories"	: {
					"age" : {
						1	: {
							"total" : 57
						}
					},...
				},
								
				"statements": {
					"p1"	:	{
						"categories"	: {
							"age" : {
								1	: {
									"positions" : {
										"-1" : 34,
										"0"	: 13,
										"1"	: 6,
										"2"	: 4
									},
									"total" : 57
							},
							...
						}
					},
					...
				}
			}
		*/
		
		// initialize the array
		
		$survey['results'] = array(
			"categories" => array(),
			"statements" => array()
		);
		
		$positions = array();
		foreach ($survey['positions'] as $pkey=>$label) {
			$positions[$pkey]=0;
		}
		
		foreach($survey['categories'] as $ckey=>$cat) {
			$survey['results']["categories"][$ckey] = array(
				"total"=>0
			);
			foreach ($cat['options'] as $val=>$opt) {
				$survey['results']["categories"][$ckey][$val] = array(
					"total"		=> 0
				);
			}
		}
		foreach($survey['statements'] as $stkey=>$stat) {
			$survey['results']["statements"][$stkey] = array(
				"categories" => array()
			);
			foreach($survey['categories'] as $ckey=>$cat) {
				$survey['results']["statements"][$stkey]["categories"][$ckey] = array();
				
				foreach ($cat['options'] as $val=>$opt) {
					$survey['results']["statements"][$stkey]["categories"][$ckey][$val] = array(
						"positions" => $positions,
						"total"		=> 0
					);
				}
			}
		}
		
		//var_dump($survey['categories']);
		
		// totals
		$query = 'SELECT count(*) as count FROM survey WHERE true ';
		if (!$request['bogus']) $query .= ' AND NOT(bogus) ';
		if ($request['filtercat']) $query .= ' AND '.$request['filtercol'].'="'.$request['filterval'].'" ';
		if ($res = $mysqli->query($query) or die($mysqli->error)) {
			while($row = $res->fetch_assoc()) {
				$survey['results']["total"]=$row['count']*1;
			}
		}
					
		
		// loop all categories
		foreach($survey['categories'] as $ckey=>$cat) {
			// do a summary sql grouping on both
			$query = 'SELECT "'.$cat['column'].'" as cat, ';
			$query .= $cat['column'].' as opt,';
			$query .= 'count(*) as count FROM survey ';
			$query .= ' WHERE '.$cat['column'].' IS NOT NULL ';
			if (!$request['bogus']) $query .= ' AND NOT(bogus) ';
			if ($request['filtercat']) $query .= ' AND '.$request['filtercol'].'="'.$request['filterval'].'" ';
			$query .= 'GROUP BY '.$cat['column'];
			
			//print '<hr>';
			//print $query;
			//print '<hr>';
			
			/* sample result
			+-------+------+-------+
			| cat   | opt  | count |
			+-------+------+-------+
			| cat01 |    1 |     2 |
			| cat01 |    4 |     1 |
			| cat01 |    5 |     2 |
			+-------+------+-------+
			*/
			if ($res = $mysqli->query($query) or die($mysqli->error)) {
				while($row = $res->fetch_assoc()) {
					if (isset($survey['results']["categories"][$ckey][$row['opt']])) {
						$survey['results']["categories"][$ckey][$row['opt']]['total'] = $row['count']*1;
						$survey['results']["categories"][$ckey]['total'] += $row['count']*1;
					} else {
						$result['messages'][] = 'found undefined category option in db: '.$row['cat'].'='.$row['opt'];
					}
					
				}
			}
		}
		
		// loop all statements
		foreach($survey['statements'] as $stkey=>$stat) {
			
			// loop all categories
			foreach($survey['categories'] as $ckey=>$cat) {


				// do a summary sql grouping on both
				$query = 'SELECT "'.$cat['column'].'" as cat, ';
				$query .= $cat['column'].' as opt,';
				$query .= '"'.$stat['column'].'" as stat,';
				$query .= $stat['column'].' as pos,';
				$query .= 'count(*) as count FROM survey ';
				$query .= ' WHERE '.$cat['column'].' IS NOT NULL ';
				if (!$request['bogus']) $query .= ' AND NOT(bogus) ';
				if ($request['filtercat']) $query .= ' AND '.$request['filtercol'].'="'.$request['filterval'].'" ';
				$query .= 'GROUP BY '.$cat['column'].','.$stat['column'];
			
				//print '<hr>';
				//print $query;
				//print '<hr>';
				
				/* sample request 
				
				SELECT "cat01" as cat, cat01 as opt,"pos01" as stat,
				 	pos01 as pos,count(*) as count 
				 FROM survey 
				 WHERE cat01 IS NOT NULL 
				 GROUP BY cat01,pos01
				
				*/
				/* sample result
				+-------+------+-------+------+-------+
				| cat   | opt  | stat  | pos  | count |
				+-------+------+-------+------+-------+
				| cat01 |    1 | pos02 |   -1 |     1 |
				| cat01 |    1 | pos02 |    0 |     1 |
				| cat01 |    2 | pos02 |    0 |     2 |
				+-------+------+-------+------+-------+
				*/
				if ($res = $mysqli->query($query) or die($mysqli->error)) {
					while($row = $res->fetch_assoc()) {
						if (isset($survey['results']["statements"][$stkey]["categories"][$ckey][$row['opt']])) {
							$resopt = &$survey['results']["statements"][$stkey]["categories"][$ckey][$row['opt']];
							$resopt["positions"][$row['pos']]=$row['count']*1;
							$resopt['total']+=$row['count']*1;
						} else {
							$result['messages'][] = 'found undefined category option in db: for '.$stkey.','.$cat['column'].'='.$row['opt'];
						}
					}
				} else {
					failure(5,"Cant read results: " . $mysqli->error);
				}
			}
		}
		
		
	}
	
	function getClientIP() {
	
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP))  return $client;
    if(filter_var($forward, FILTER_VALIDATE_IP)) return $forward;
    return $remote;

	}
	
	function json_error() {
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				$error = ' - No errors';
			break;
			case JSON_ERROR_DEPTH:
				$error =  ' - Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				$error =  ' - Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
				$error =  ' - Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
				$error =  ' - Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
				$error =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
				$error =  ' - Unknown error';
			break;
		}
		return $error;
	}
	
	function failure($code,$message) {
		global $request,$result;
		
		$result['code'] 	= $code;
		$result['success']  = false;
		$result['messages'][] = $message;
		respond();
	
	}
	function success() {
		global $request,$result;
		$result['success'] = true;
		respond();
	
	}
	function respond() {
		global $config,$request,$result;
		
		if ($result['success'] && $request['format']=='html' && !isset($result['html'])) {
			$result['html'] = '<xmp>'.json_encode($result,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT).'</xmp>';
		}
		if ($request['format']=='csv' && !isset($result['csv'])) {
			$result['csv'] = json_encode($result,JSON_FORCE_OBJECT);
		}
		
		$response = array(
			'request' 	=> $request,
			'result'	=> $result
		);
		
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Access-Control-Allow-Origin: ".$config->cors);
		
		switch($request['format']) {
			case 'json':
				header('Content-Type: application/json; charset=utf-8');
				print json_encode($response,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
				break;
			case 'csv':
				$stamp = date('Ymd');
				header('Content-Type: text/csv; charset=utf-8');
				header("Content-Disposition: attachment; filename=survey-engine-$stamp.csv");
				print $result['csv'];
				break;
			case 'html':
				
				header('Content-Type: text/html; charset=utf-8');
				include 'interface.php';
				break;
			default:
				var_dump($response);
		}
		exit(0);
	}
?>
