<?php
//
// Script php pour l'envoi de données météo vers MeteoBelgique
// depuis le logiciel WeeWX avec base de données MySQL.
// Infos: https://github.com/MeteoBelgique/MB_WeeWX
//

//
// Script principal, ne rien modifier.
//

require_once('config.php');

date_default_timezone_set('Europe/Brussels');

$db = new mysqli($config['sqlServer'], $config['sqlUser'], $config['sqlPassword'], $config['sqlDatabase']);
$sql = "SELECT dateTime, usUnits, outTemp, outHumidity, barometer, dewpoint, radiation, UV, windGust, windDir, rain, soilTemp1";

if($config['getHiLo']){
    $sql .= ", lowOutTemp, highOutTemp";   
}

$sql .= " FROM ".$config['sqlTable']." ORDER BY dateTime DESC LIMIT ".($config['days']*24*60)/$config['interval'];

$result = $db->query($sql);

$out = array();

//Headers
$fields = mysqli_num_fields($result);
for($i = 0; $i < $fields; $i++){
        $fieldInfo = $result->fetch_field_direct($i);
        $out[0][$i] = $fieldInfo->name;
}

//Data and conversions
$count = 1;
while($row = $result->fetch_assoc()){
    if($row['outTemp'] == NULL || $row['outHumidity'] == NULL){
        continue;
    }
    
    $out[$count] = array();

    $units = $row['usUnits'];
    $row['dateTime'] = date('Y-m-d H:i', $row['dateTime']);

	if($units == 1){
		$row['outTemp'] = round(($row['outTemp']-32)*5/9, 1);
		$row['dewpoint'] = round(($row['dewpoint']-32)/5/9, 1);
		$row['barometer'] = round($row['barometer']*33.8639, 1);
		$row['windGust'] = round($row['windGust']*1.60934, 1);
		$row['rain'] = round($row['rain']*25.4, 1);
		$row['soilTemp1'] = round(($row['soilTemp1']-32)*5/9, 1);
        
        if($config['getHiLo']){
            $row['lowOutTemp'] = round(($row['lowOutTemp']-32)*5/9, 1);
            $row['highOutTemp'] = round(($row['highOutTemp']-32)*5/9, 1);
        }
	}else if($units == 16){
		$row['outTemp'] = round($row['outTemp'], 1);
		$row['dewpoint'] = round($row['dewpoint'], 1);
		$row['barometer'] = round($row['barometer'], 1);
		$row['windGust'] = round($row['windGust'], 1);
		$row['rain'] = round($row['rain']*10, 1);
		$row['soilTemp1'] = round($row['soilTemp1'], 1);
        
        if($config['getHiLo']){
            $row['lowOutTemp'] = round($row['lowOutTemp'], 1);
            $row['highOutTemp'] = round($row['highOutTemp'], 1);
        }
	}else if($units == 17){
		$row['outTemp'] = round($row['outTemp'], 1);
		$row['dewpoint'] = round($row['dewpoint'], 1);
		$row['barometer'] = round($row['barometer'], 1);
		$row['windGust'] = round($row['windGust']*3.6, 1);
		$row['rain'] = round($row['rain'], 1);
		$row['soilTemp1'] = round($row['soilTemp1'], 1);
        
        if($config['getHiLo']){
            $row['lowOutTemp'] = round($row['lowOutTemp'], 1);
            $row['highOutTemp'] = round($row['highOutTemp'], 1);
        }
	}

	$row['windDir'] = round($row['windDir'], 1);
	$row['UV'] = round($row['UV'], 1);
	$row['radiation'] = round($row['radiation'], 0);
	$row['outHumidity'] = round($row['outHumidity'], 0);

    foreach($row as $value){
        array_push($out[$count], $value);
    }
    $count++;
}

//Prepare buffer for output
$buffer = fopen('php://memory', 'r+');

//Formating data as csv
foreach ($out as $fields) {
    fputcsv($buffer, $fields);
}

rewind($buffer);

//FTP transfer
$conn_id = ftp_connect($config['ftpHost']) or die("could not connect to ".$config['ftpHost']);
if (!@ftp_login($conn_id, $config['ftpUser'], $config['ftpPassword'])) { die("could not connect to ".$config['ftpHost']. "invalid username and/or password");}
ftp_pasv($conn_id, true);
$remote="data.csv";
if(ftp_fput($conn_id, $remote, $buffer, FTP_ASCII)){
	echo "Update successfull !\n";
}else{
	echo "Error while uploading file...\n";
}
ftp_close($conn_id);

//Closing
fclose($buffer);
$db->close();
?>
