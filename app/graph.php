<?php defined('CONFIG') || define('CONFIG', parse_ini_file('../app/config.ini', true));

// simple rpn arithmatic evaluations
function rpn($expression, $stack = [], $op = []) {
  $op['+'] = function($a, $b) { return $a  + $b; };
  $op['*'] = function($a, $b) { return $a  * $b; };
  $op['^'] = function($a, $b) { return $a ** $b; };
  $op['-'] = function($a, $b) { return $a  - $b; };
  $op['/'] = function($a, $b) { return $a  / $b; };
  
  foreach (preg_split("/[^-+*\/^.0-9]+/", trim($expression)) as $token) {
    $stack[] = is_numeric($token) ? $token : $op[$token](...array_slice($stack, -2));
  }
  
  return end($stack);
}

function createGraph($filename, $key, $start, $trend, $label, $title, $autoscale, $lower = 0, $upper = 0) {
  $graphObj = new RRDGraph("charts/{$filename}.png");

  $options = array_merge(CONFIG['chart']['params'], [
    "--start"          => $start,
    '--vertical-label' => $label,
    '--title'          => $title,
    '--alt-y-grid',
  ]);
  
  if ($lower < $upper) {
    $options['-l'] = $lower;
    $options['-u'] = $upper;
  } else if ($autoscale) {
    $options[] = '-A';
  }
  
  # Show the data, or a moving average trend line, or both.
  $options[] = sprintf('DEF:dSeries=%s:%s:AVERAGE', CONFIG['database'], $key);
  
  if ($trend == 0) {
    $options[] = 'LINE2:dSeries#0400ff';
  } elseif ($trend == 1) {
    $options[] = 'CDEF:smoothed=dSeries,86400,TREND';
    $options[] = 'LINE2:smoothed#0400ff';
  } elseif ($trend == 2) {
    $options[] = 'LINE1:dSeries#0400ff';
    $options[] = 'CDEF:smoothed=dSeries,86400,TREND';
    $options[] = 'LINE2:smoothed#0400ff';
  }

  // Reverse Polish Notation
  // A,B,C,IF should be read as if (A) then (B) else (C); n q GE should be read as n >= q ? 1 : 0
  if ($key == 'windspeedmph') {
    $ord = array_keys(CONFIG['chart']['o_color']);
    
    $options[] = sprintf('DEF:wDir=%s:winddir:AVERAGE', CONFIG['database']);
    $options[] = 'VDEF:wMax=dSeries,MAXIMUM';
    $options[] = 'CDEF:wMaxScaled=dSeries,0,*,wMax,+,-0.15,*';
    
    for ($s = 45, $i=$s/2; $i < 360; $i+=$s) {
      $options[] = sprintf('CDEF:%sdir=wDir,%s,GE,wDir,%s,LT,*,wMaxScaled,0,IF', $ord[floor($i/$s)], fmod($i - $s + 360, 360), $i);
    }
    
    array_push($options, ...array_map(function($ord, $hex) {
      return sprintf('AREA:%sdir%s:%s', $ord, $hex, strtoupper($ord));
    }, $ord , CONFIG['chart']['o_color']));
  }
  
  $graphObj->setOptions($options);
  $graphObj->save();
}


function generateSeries($graphs, $length = '1d') {
  foreach ($graphs as $type => $params) {
    createGraph("{$length}_{$type}", $type, "end-$length", 0, ...$params);
  }
}