<html>
<head>
	<title>V2 Reports</title>
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
			width: 1400px;
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
		}
	</style>
</head>

<body class="bg-darker fg-white align-center">
	<div class="page-content">
	<?php if(!isSet($_GET['id'])) { ?>
		<div>
				<div class="fg-lightWhite bg-darkCobalt" style="padding: 12px">
				<div style="font-size: 2rem" class="fg-white text-shadow metro-title text-light"><span class="fg-white mif-rocket margin5"></span>V2 Reports <span class="text-small">Beta</span></div></br>
					<form action="" method="get">
						<div>Search for reported for players</div>
						<input type="text" name="search" minlength="3" maxlength="12" value="<?php if(isSet($_GET['search'])) {echo $_GET['search'];}?>" />
						<input type="submit"/>
					</form>
					<div>
						<?php if(!isSet($_GET['search'])) { echo '<a href="index.php" class="fg-yellow" style="text-decoration: underline">Home</a>';}
						else { echo '<a href="reports.php" class="fg-yellow" style="text-decoration: underline">Back</a>';}?>
					</div>
				</div>
		</div>
		   <div class="no-overflow">
				<div class="grid">
					<div class="row cells6">
<?php
}
function presenceData($id) {
	$pApi = file_get_contents('https://v2.mcsebi.ru/api/presenceData/' . $id);
	$pstripApi = stripcslashes(fixApiResult($pApi));
	$pjsonApi = json_decode($pstripApi);
	
	return $pjsonApi;
}
function fixApiResult($str) {
	$result = str_replace('result":"','result":',$str);	
	$final = str_replace('","connection_data',',"connection_data',$result);	
	return $final;
}							
function getColoredName($host){
	$count_rpl = 0;
	$hostname = $host;		
	$h2 = str_replace('^1','<span class="w2red">', $hostname, $count_rpl_1);
	$h3 = str_replace('^2','<span class="w2green">', $h2, $count_rpl_2);
	$h4 = str_replace('^3','<span class="w2yellow">', $h3, $count_rpl_3);
	$h5 = str_replace('^4','<span class="w2blue">', $h4, $count_rpl_4);
	$h6 = str_replace('^5','<span class="w2marine">', $h5, $count_rpl_5);
	$h7 = str_replace('^6','<span class="w2pink">', $h6, $count_rpl_6);
	$h8 = str_replace('^7','<span class="fg-white">', $h7, $count_rpl_7);
	$h9 = str_replace('^8','<span class="fg-gray">', $h8, $count_rpl_8);
	$h10 = str_replace('^0','<span class="fg-grayLight">', $h9, $count_rpl_9);
	$final = str_replace('^9','<span class="fg-white">', $h10, $count_rpl_10);

	$count = $count_rpl_1 + $count_rpl_2 + $count_rpl_3 + $count_rpl_4 + $count_rpl_5 + $count_rpl_6 + $count_rpl_7 + $count_rpl_8 + $count_rpl_9 + $count_rpl_10;

	$final = $final . str_repeat('</span>', $count);
	return $final;
}
require 'inc/connect.php';
$failDB = 'Could not establish a connection to the database.</br>';

$sql = "SELECT * FROM reports GROUP BY his_id ORDER BY id DESC LIMIT 20";					
$result = mysql_query($sql);

if(!$result) {
	echo $failDB;
}
else {
	while ($row = mysql_fetch_assoc($result))
	if (!isSet($_GET['search'])) 	{
		if(!isSet($_GET['id'])) {
			
			// count how many uploads each report have
			$csql = 'SELECT *
					FROM reports
					WHERE his_id="'.$row['his_id'].'"';
			$cresult = mysql_query($csql);
			$count = mysql_num_rows($cresult);
			
			?>
			
			<section class="thumb-section margin10">
			<div class="thumbnail">
				<a href="?search=<?php echo $row['player']; ?>"><img src="screenshots/<?php echo $row['proof']; ?>"></a>
			  </div>
			<div class="thumb-content">
				<div class="thumb-title">
					<a style="padding-left: 4px;padding-right: 4px" class="fg-white bg-red text-shadow" href="?search=<?php echo $row['player']; ?>"><?php echo getColoredName($row['player']);?></a>
				</div>
				<div class="thumb-desc">
					<span class="place-left"><?php echo getColoredName($row['server']); ?></span>
					<span class="place-right"><?php echo date('d, M Y', strtotime($row['date'])); ?></span>
				</div>
			</div>
			</section>	
			
		<?php
		}
	}
}

// search
if (isSet($_GET['search'])) {
	
	$search = $_GET['search'];
	$search = "SELECT * FROM reports WHERE player LIKE '%".$search."%' ORDER BY date DESC"; 
	$search = mysql_query($search);
	
if(!$search) {
	echo $failDB;
}
else {
		while ($row = mysql_fetch_array($search))
		{
			if(!isSet($_GET['id'])) { ?>
			
			<section class="thumb-section margin10">
			<div class="thumbnail">
				<a href="?id=<?php echo $row['id']; ?>"><img src="screenshots/<?php echo $row['proof']; ?>"></a>
			  </div>
			<div class="thumb-content">
				<div class="thumb-title">
					<a style="padding-left: 4px;padding-right: 4px" class="fg-white bg-red text-shadow" href="?search=<?php echo $row['player']; ?>"><?php echo getColoredName($row['player']);?></a>
				</div>
				<div class="thumb-desc">
					<span class="place-left"><?php echo getColoredName($row['server']); ?></span>
					<span class="place-right"><?php echo date('d, M Y', strtotime($row['date'])); ?></span>
				</div>
			</div>
			</section>	
			
			<?php
			}
		}
	}
}

if(isSet($_GET['id'])) {

	$id = mysql_real_escape_string($_GET['id']);
	$sql = 'SELECT * 
	FROM reports 
	WHERE id='. $id .'';
	
	$viewsql = mysql_query('SELECT * 
	FROM views
	WHERE view_ip = "'.$_SERVER['REMOTE_ADDR'].'" 
	AND view_report = "'.$_GET['id'].'"');
	
	if(!$viewsql) {
		echo $failDB;
	}
	else {
		$view_count_sql = mysql_query('SELECT SUM(view_count) AS total_views FROM views WHERE view_report = "'.$_GET['id'].'"');
		$view_row = mysql_fetch_assoc($view_count_sql);
		$view_count = $view_row['total_views']; // FUCKING COUNT ROW

		if(mysql_num_rows($viewsql) == 0) {
			$insertView = 'INSERT INTO views (view_report, view_ip, view_count, view_date, view_recent_date)VALUES("'.$_GET['id'].'", "'.$_SERVER['REMOTE_ADDR'].'", "1", NOW(), NOW())';		  
			mysql_query($insertView);
		}
		else if(mysql_num_rows($viewsql) >= 1) {
			$updateView = ('UPDATE views SET view_count=view_count+1, view_recent_date=NOW() WHERE view_report="'.$_GET['id'].'" AND view_recent_date < (DATE_SUB(NOW(), INTERVAL 8 HOUR))');
			mysql_query($updateView);
		}
	}
	
	$result = mysql_query($sql);
	if(!$result) {
		echo $failDB;
	}
	else {
		$row = mysql_fetch_assoc($result); ?>
	
		<div class="align-center">	
		<h1><?php echo getColoredName($row['player']); ?> 
			<img src="images/prestige_<?php echo $row['prestige']; ?>.png" title="Prestige" width="32"/> 
			<span style="font-size: 10px; color: #888;" title="Player ID">(<?php echo $row['his_id']; ?>)</span>
		</h1>
		<div>Reported on <?php echo date('d, M Y', strtotime($row['date'])); ?> when he was playing 
			<span class="uppercase"><?php echo $row['game']; ?></span> 
			<?php if(!empty($row['server'])) {echo 'on ' . getColoredName($row['server']);} ?>
		</div></br>
		<center>
		<div class="playerScreen">
			<img src="screenshots/<?php echo $row['proof']; ?>"/>
		</div></center></br>
		<?php if($view_count != 0) {echo $view_count . ' views';} ?></br></br>
		<a href="reports.php" class="fg-yellow" style="text-decoration: underline">Back to Reports</a>
	</div>
	
<?php 
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
</html>