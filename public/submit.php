<?php define('CONFIG', parse_ini_file('../bin/config.ini', true));

// Local networks only
if (levenshtein($_SERVER["SERVER_ADDR"], $_SERVER["REMOTE_ADDR"]) > 3) exit();

// sample request = http://weather.local/submit.php?ws=10.0&wd=270&ws2=7.0&wd2=270&gs=25.0&gd=180&gs10=12.0&gd10=270&h=51.0&t=76.8&p=101269.3&r=1.00&dr=5.00&b=4.3&l=2.4


$status = "!ok\n";

$_GET['p'] = $_GET['p'] * CONFIG['coefficients']['pascal_conversion'] + CONFIG['coefficients']['barometric_correction'];
$_GET['l'] = $_GET['l'] / CONFIG['coefficients']['light_sensor_factor'] * 100;

$data = array_map(function($map) {
  return $_GET[$map];
}, [
  'windspeedmph'       => 'ws',
  'winddir'            => 'wd',
  'windgustmph'        => 'gs',
  'windgustdir'        => 'gd',
  'windspeedmph_avg2m' => 'ws2',
  'winddir_avg2m'      => 'wd2',
  'windgustmph_10m'    => 'gs10',
  'windgustdir_10m'    => 'gd10',
  'humidity'           => 'h',
  'tempf'              => 't',
  'rainin'             => 'r',
  'dailyrainin'        => 'dr',
  'pressure'           => 'p',
  'batt_lvl'           => 'b',
  'light_lvl'          => 'l',
]);


// check for reboots

if ($data['pressure'] < 25.0 || $data['pressure'] > 35.0) {
  // invalid presure value
}

if ($data['tempf'] < -100) {
  // invalid temperature
  $status = "!r\n";
}

if ($data['humidity'] > 105 || $data['humidity'] < 0) {
  // invalid humidity
  $status = "!r\n";
}

try {
  $updator = new RRDUpdater(CONFIG['database']);
  $updator->update([
    'windspeedmph' => (float) $data['windspeedmph_avg2m'],
    'winddir'      => (float) $data['winddir_avg2m'] * 22.5,
    'tempf'        => (float) $data['tempf'],
    'rainin'       => (float) $data['rainin'],
    'pressure'     => (float) $data['pressure'],
    'humidity'     => (float) $data['humidity'],
    ], $_SERVER['REQUEST_TIME']);
} catch (Exception $e) {
  echo "ERROR: {$e->getMessage()}\n";
  print_r($e);
}


// todo - create a perpetual log file of weather, probably as xml for query-ability.

echo $status;



        
        
