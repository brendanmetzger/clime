<?php define('CONFIG', parse_ini_file('config.ini', true));

// NOTE: time values are in SECONDS: 86400 in a day, 3600 in an HOUR, 60 in a minute...
$creator  = new RRDCreator(CONFIG['database'], strtotime(CONFIG['created']), CONFIG['interval']);

foreach (CONFIG['chart']['fields'] as $field) {
  $creator->addDataSource(sprintf('%s:GAUGE:%s:U:U', $field, CONFIG['interval'] * 2));
}


$creator->addArchive("AVERAGE:0.5:1:2880");   // 2-day avg w/ 1min step: (86400 / 60) * 2 = 2880
$creator->addArchive("AVERAGE:0.5:30:18000"); // ~annual avg w/ 30 min step: 86400 / (30 * 60) * 375 = 18k

$creator->save();