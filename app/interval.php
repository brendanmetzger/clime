<?php define('CONFIG', parse_ini_file('config.ini', true));

// this is just to simulate what pinging the server would look like over an hour
function getInput() {
  return [
    'ws'   => rand(5, 50),
    'wd'   => rand(0, 15),
    'ws2'  => rand(5, 10),
    'wd2'  => rand(0, 15),
    'gs'   => rand(30, 40),
    'gd'   => rand(0, 360),
    'gs10' => rand(10, 50),
    'gd10' => rand(0, 15),
    'h'    => rand(50, 80),
    't'    => rand(20, 100),
    'p'    => rand(20, 30),
    'r'    => rand(1, 3),
    'dr'   => rand(2, 6),
    'b'    => rand(2, 10),
    'l'    => rand(40, 90),
  ];
}

for ($i=0; $i < 60; $i++) { 
  echo file_get_contents(sprintf('http://%s/submit.php?$s', CONFIG['serverip'], http_build_query(getInput())));
  sleep(60);
}