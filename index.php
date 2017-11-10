<?php date_default_timezone_set('America/Sao_Paulo'); ?>
<html>
<head>
	<title>V2 Screenshot</title>
	<link href="css/main.css" rel="stylesheet">
	<link href="css/metro.css" rel="stylesheet">
    <link href="css/metro-icons.css" rel="stylesheet">
    <link href="css/metro-responsive.css" rel="stylesheet">
    <link href="css/metro-schemes.css" rel="stylesheet">

    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/jquery.popupoverlay.js"></script>
    <script src="js/metro.js"></script>
	<style>
		form {
			display: inline;
		}		
		.playerScreen {
			width: 1200px;
			height: 675px;
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
			display: none;
		}
	</style>
	
	<script>
    $(document).ready(function() {
		$('#reportPopup').popup({
			opacity: 0,
			transition: 'all 0.3s'
			});
		});
	
	var Counter = 1;
	function requestScreenshot( ID, Name ) {
		$.getJSON( "https://v2.mcsebi.ru/api/screenshot/" + ID, 
		function( Response ) {
			if ( !getScreenshot( Name, ID, Response ) ) {
				setTimeout( function() {
					requestScreenshot( ID, Name )
				}, 500 );
			}
		});
	}

	function getScreenshot( Name, ID, Response ) {			
			if ( Response.status == "204" && Response.result == "Offline" ) { // player not connected to NP at this time
				$( '#status' ).html( 'This player is currently offline!' );
				return true;
			}
				
			else if ( Response.status == "204" && Response.result == "Request sent."  || Response.result == "Waiting for answer." ) { // NP is requesting screenshot ( should be 202 @IAIN )
				var waitStr = "....";
				$( '#status' ).html( 'Waiting for screenshot' + waitStr.substring( 0, Counter % 4 ) );
				Counter++;
				return false;
			}
						
			else if ( Response.status == "200" ) { // request complete
				$('#playerScreen').prepend('<input type="hidden" name="screenshot" value="data:image/jpeg;base64,' + Response.result + '" /><img id="playerScreen" src="data:image/jpeg;base64,' + Response.result +'" />').fadeIn( 'fast' );
				$('#reportButton').css('display', 'block');
				$( '#status' ).html( '' );
							return true;
			}
			
			else if ( Counter >= 30 ) { 
				$( '#status' ).html( 'No screenshot received!' );
				return true;
			}
			
			else { 
				$( '#status' ).html( 'Invalid request!' );
				return true;
			}
	}
	</script>
</head>
<body class="bg-darker fg-white">
    <div class="page-content">
		<div class="align-center">		
				<div class="fg-lightWhite bg-darkred" style="padding: 12px">
				<div style="font-size: 2rem" class="fg-white text-shadow metro-title text-light"><span class="fg-white mif-rocket margin5"></span>V2 Screenshot  <span class="text-small">Beta</span></div></br>
					<form action="" method="get">
						<div>Search for players</div>
						<input type="text" name="keyword" minlength="3" maxlength="12" required />
						<input type="submit"/>
					</form>
				<div><a href="reports.php" class="fg-yellow" style="text-decoration: underline">Reports</a></div>
				</div>
            <div class="container">
                <div class="no-overflow">
					<div class="grid">
							<?php
							
							function getColoredName($host){
								$count_rpl = 0;
								$hostname = $host;		
								$h2 = str_replace('^1','<span class="fg-red">', $hostname, $count_rpl_1);
								$h3 = str_replace('^2','<span class="fg-green">', $h2, $count_rpl_2);
								$h4 = str_replace('^3','<span class="fg-yellow">', $h3, $count_rpl_3);
								$h5 = str_replace('^4','<span class="fg-blue">', $h4, $count_rpl_4);
								$h6 = str_replace('^5','<span class="fg-cyan">', $h5, $count_rpl_5);
								$h7 = str_replace('^6','<span class="fg-purple">', $h6, $count_rpl_6);
								$h8 = str_replace('^7','<span class="fg-black">', $h7, $count_rpl_7);
								$h9 = str_replace('^8','<span class="fg-gray">', $h8, $count_rpl_8);
								$h10 = str_replace('^0','<span class="fg-black">', $h9, $count_rpl_9);
								$final = str_replace('^9','<span class="fg-white">', $h10, $count_rpl_10);

								$count = $count_rpl_1 + $count_rpl_2 + $count_rpl_3 + $count_rpl_4 + $count_rpl_5 + $count_rpl_6 + $count_rpl_7 + $count_rpl_8 + $count_rpl_9 + $count_rpl_10;

								$final = $final . str_repeat('</span>', $count);
								return $final;
							}
							/*function fixApiResult($str) {
								// the arrays
								$useless = array("__body", "game", "hostname","current_server","netcodeVersion","3141","serverType","99","T_76561197960265795","1","Offline");
								$gameCode = array("iw4m", "t5mp", "t5sp");
								$gameName = array("Modern Warfare 2", "Black Ops", "Black Ops");
								
								// the replace
								$first = str_replace($useless, " ", $str);
								$second = str_replace($gameCode, $gameName, $first);								
								$final = preg_replace('/[^A-Za-z0-9\. -]/', '', $second);
								
								return '<span class="mif-gamepad"></span> ' . $final;
							}*/
							function fixApiResult($str) {
								$result = str_replace('result":"','result":',$str);	
								$final = str_replace('","connection_data',',"connection_data',$result);	
								return $final;
							}							
							function presenceData($id) {
								$pApi = file_get_contents('https://v2.mcsebi.ru/api/presenceData/' . $id);
								$pstripApi = stripcslashes(fixApiResult($pApi));
								$pjsonApi = json_decode($pstripApi);
								
								return $pjsonApi;
							}
							
							if(isSet($_GET['keyword'])) {
								$keyword = $_GET['keyword'];

								$uApi = file_get_contents('https://v2.mcsebi.ru/api/username/' . $keyword);
								$ujsonApi = json_decode($uApi);
								
								$i = 0;								
								if(!empty( $ujsonApi->result)){
									foreach($ujsonApi->result as $result) 
									{
										//if (++$i == 16) break;
										
										//if(presenceData($result->user_id)->status == '200') {
											echo '<form action="" method="get">
														<input type="hidden"  name="id" value="' . $result->user_id . '"/>
														<input type="hidden"  name="username" value="' . $result->username . '"/>													
														<button class="button" type="submit">' . getColoredName($result->username) . '</button>
													</form>';						
										//}
										/*else {
											echo '<button class="button danger" style="margin-right: 6px; margin-left: 6px;" type="submit">' . getColoredName($result->username) . '</button>';
										}*/
									}
								}
								else {
									echo 'We couldn\'t find any data with this username';
								}
								$_POST['id'] = null;
							}
							else if(!isSet($_GET['id'])) {
								echo 'Give us some username to check</br>								
											<div class="margin20">
												Note:</br>
												 <a href="http://alexclaz.com/screenshot/reports.php?id=9">This report</a> is a screenshot glitch, not a hack, take another one.
											</div>';
							}
							?>
					<?php
					if(isSet($_GET['id'])) {
						
						echo '<form action="" method="get">
									<input type="hidden"  name="id" value="' . $_GET['id'] . '"/>
									<input type="hidden"  name="username" value="' . $_GET['username'] . '"/>
									<button class="button" type="submit">' . getColoredName($_GET['username']) . ' <small style="font-size: 9px; color: #999">' . $_GET['id'] . '</small></button>
								</form></br></br>';
								
								/*$pApi = file_get_contents('https://v2.mcsebi.ru/api/presenceData/' . $_GET['id']);
								$pstripApi = stripcslashes(fixApiResult($pApi));
								$pjsonApi = json_decode($pstripApi);*/
								
								
								if(presenceData($_GET['id'])->status != '200') {
									echo 'Player Offline';
								}
								
								else {
									$server = presenceData($_GET['id'])->result->hostname; 
									$game = presenceData($_GET['id'])->result->game; 
									$prestige = presenceData($_GET['id'])->connection_data->Prestige; 
									
									echo '<div class="row cells2">
												<div class="cell">
													<div>Playing <span class="uppercase">' . $game  . '</span></div>
													<div class="margin5"><img src="images/prestige_' . $prestige  . '.png" title="Prestige" width="16"/></div>
												</div>

												<div class="cell">' . 
												getColoredName($server) . '
												</div>
											</div>';								
								?>
									<div id="reportButton" style="display: none;">
									<button class="button small-button danger reportPopup_open">Report</button>
										<div id="reportPopup" class="ribbed-grayLighter padding10">
											<button class="button danger" type="submit" name="reported" form="report">Yes, he is hacking!</button>
											<button class="button success reportPopup_close">Nah, I don't think so</button>
										</div>
									</div>
									
									<div>
										<h1 id="status">Please Wait.</h1>
											<form action="" method="post" id="report">
												<div border="5" id="playerScreen" name="playerScreen">
													<script>requestScreenshot('<?php echo $_GET['id'] . "','" . $_GET['username']?>');</script>
													<input type="hidden" name="server" value="<?php echo $server; ?>"/>
													<input type="hidden" name="game" value="<?php echo $game; ?>"/>
													<input type="hidden" name="prestige" value="<?php echo $prestige; ?>"/>
												</div>
											</form>
									</div>

									<?php 
									include 'inc/connect.php';
									
									if(isSet($_POST['reported'])) {										
									$img = $_POST['screenshot']; 
									$img = str_replace('data:image/jpeg;base64,', '', $img);
									$img = str_replace(' ', '+', $img);
									$data = base64_decode($img);
									$filename = $_GET['username'].'_'.md5(rand(3,5) * time($_POST['screenshot'])).'.jpeg';
									file_put_contents('screenshots/'.$filename, $data);
										
										
									//do the comment
									$sql = 'INSERT INTO 
										reports (proof,
										player,
										his_id,
										server,
										game,
										prestige,
										your_ip,
										date
										) VALUES ("'. $filename .'",
										"' . mysql_real_escape_string($_GET['username']) . '", 
										"' . mysql_real_escape_string($_GET['id']) . '",
										"' . mysql_real_escape_string($_POST['server']) . '",
										"' . mysql_real_escape_string($_POST['game']) . '",
										"' . mysql_real_escape_string($_POST['prestige']) . '",
										"' . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . '",
										NOW())';
										
										$sqlresult = mysql_query($sql);
										if(!$sqlresult) {
											echo '<div class="padding10 fg-red">Something went wrong. Please try again later.</div>';
										}
										else {
											$id = mysql_insert_id(); // get mod id -- not working atm		
											?>
											<script>
											$.Notify({
												caption: 'Reported!',
												content: 'Click <a class="fg-white" style="text-decoration: underline;" href="reports.php?id=<?php echo $id; ?>" target="_blank">here</a> to see the report',
												type: 'success',
												keepOpen: true
											});
											</script>											
											<?php
										}
									}
								}
					}
					?>
					</div>
				</div>
			</div>
		</div>
	</div>
<div data-role="footer" data-position="fixed">
        <div class="align-center text-small padding20">
            Php page made by <a href="https://twitter.com/AlexCLaz">Alex CLaz</a></br>
			JavaScript screenshot by <a href="http://raidmax.org/">RaidMax</a></br>
        </div>
</div>
</body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-54856090-9', 'auto');
  ga('send', 'pageview');
</script>
</html>