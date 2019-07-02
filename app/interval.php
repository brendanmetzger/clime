<?php define('CONFIG', parse_ini_file('config.ini', true));

// this is just to simulate what pinging the server would look like over an hour
function getInput() {
  return [
    'ws'   => (float)rand(5, 50),
    'wd'   => (float)rand(0,16),
    'ws2'  => (float)rand(5, 10),
    'wd2'  => (float)round(date('i') / 60 * 15),
    'gs'   => (float)rand(30, 40),
    'gd'   => (float)round(date('i') / 60 * 15),
    'gs10' => (float)rand(10, 50),
    'gd10' => (float)round(date('i') / 60 * 15),
    'h'    => (float)rand(50, 80),
    't'    => (float)rand(20, 100),
    'p'    => (float)rand(20, 30),
    'r'    => (float)rand(2, 3),
    'dr'   => (float)rand(2, 6),
    'b'    => (float)rand(2, 10),
    'l'    => (float)rand(40, 90),
  ];
}

function progressBar($done, $total) {
  $perc = floor(($done / $total) * 100);
  $left = 100 - $perc;
  $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");
  fwrite(STDERR, $write);
}

for ($i=0; $i < 120; $i++) { 
  echo "sending request #{$i}\n";
  $url = sprintf('http://%s/submit.php?%s', CONFIG['serverip'], http_build_query(getInput()));
  echo "url is {$url}\n";
  echo file_get_contents($url);
  echo "sleeping for 60 seconds\n";
  sleep(60);
}