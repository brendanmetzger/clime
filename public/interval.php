<?php define('CONFIG', parse_ini_file('../app/config.ini', true));

array_key_exists('USER', $_SERVER) || die('use this on the command line <code>&gt;&gt;&gt; php interval.php</code>');

// this is just to simulate what pinging the server would look like over a few hours
function getInput() {
  return [
    'ws'   => rand(5, 50),
    'wd'   => round(date('i') / 60 * 15),
    'ws2'  => rand(5, 10),
    'wd2'  => round(date('i') / 60 * 15),
    'gs'   => rand(30, 40),
    'gd'   => round(date('i') / 60 * 15),
    'gs10' => rand(10, 50),
    'gd10' => round(date('i') / 60 * 15),
    'h'    => rand(50, 80),
    't'    => rand(20, 100),
    'p'    => rand(20, 30),
    'r'    => rand(1, 3),
    'dr'   => rand(2, 6),
    'b'    => rand(2, 10),
    'l'    => rand(40, 90),
  ];
}

for ($i=0; $i < 120; $i++) { 
  echo "sending request #{$i}\n";
  $url = sprintf('http://%s/submit.php?%s', CONFIG['serverip'], http_build_query(getInput()));
  echo "url is {$url}\n";
  echo file_get_contents($url);
  echo "sleeping for 10 seconds\n";
  sleep(10);
}