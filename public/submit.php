<?php define('CONFIG', parse_ini_file('../app/config.ini', true));

$status = "!ok\n";

// Local networks only
if (levenshtein($_SERVER["SERVER_ADDR"], $_SERVER["REMOTE_ADDR"]) > 3) exit();


require_once '../app/graph.php';

// sample request = http://HOST/submit.php?ws=10.0&wd=10&ws2=7.0&wd2=270&gs=25.0&gd=180&gs10=12.0&gd10=270&h=51.0&t=76.8&p=101269.3&r=1.00&dr=5.00&b=4.3&l=2.4

$_GET['p']  = rpn(sprintf(CONFIG['formula']['pressure'], $_GET['p']));
$_GET['l']  = rpn(sprintf(CONFIG['formula']['light'], $_GET['l']));
$_GET['wd'] = rpn(sprintf(CONFIG['formula']['degrees'], $_GET['wd']));

$data = array_map(function($map) {
  return (float) $_GET[$map];
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
  // not all fields are saved to the data RRD database, intersect fields avail in db
  $fields = array_intersect_key($data, array_flip(CONFIG['chart']['fields']));
  if (true) {
    // TODO make this run less...
    $graphs = [
      'windspeedmph' => ['Miles per Hour', 'Sustained Wind', true],
      'tempf'        => ['Degrees Farenheit', 'Temperature', true],
      'pressure'     => ['inches Hg', 'Barometric Pressure', true],
      'humidity'     => ['Percent', 'Relative Humidity', true],
      'rainin'       => ['Inches', 'Rain Fall', false],
    ];
    
    generateSeries($graphs, '1d');
    // generateSeries($graphs, '10days');
    // generateSeries($graphs, '3months');
    // generateSeries($graphs, '12months');
  }
  $updator->update($fields, $_SERVER['REQUEST_TIME']);
} catch (Exception $e) {
  echo "ERROR: {$e->getMessage()}\n";
  print_r($e);
}

// TODO - create a perpetual log file of weather, probably xml for queries.
echo $status;