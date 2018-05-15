<?php define('CONFIG', parse_ini_file('../app/config.ini', true));


function createGraph($filename, $dataItem, $start, $trend, $label, $title, $autoscale, $lower = 0, $upper = 0) {
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
  $options[] = sprintf('DEF:dSeries=%s:%s:AVERAGE', CONFIG['database'], $dataItem);
  
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

  # if wind plot show color coded wind direction
  if ($dataItem == 'windspeedmph') {
      $options[] = sprintf('DEF:wDir=%s:winddir:AVERAGE', CONFIG['database']);
      $options[] = 'VDEF:wMax=dSeries,MAXIMUM';
      $options[] = 'CDEF:wMaxScaled=dSeries,0,*,wMax,+,-0.15,*';
      $options[] = 'CDEF:ndir=wDir,337.5,GE,wDir,22.5,LE,+,wMaxScaled,0,IF';
      $options[] = 'CDEF:nedir=wDir,22.5,GT,wDir,67.5,LT,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:edir=wDir,67.5,GE,wDir,112.5,LE,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:sedir=wDir,112.5,GT,wDir,157.5,LT,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:sdir=wDir,157.5,GE,wDir,202.5,LE,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:swdir=wDir,202.5,GT,wDir,247.5,LT,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:wdir=wDir,247.5,GE,wDir,292.5,LE,*,wMaxScaled,0,IF';
      $options[] = 'CDEF:nwdir=wDir,292.5,GT,wDir,337.5,LT,*,wMaxScaled,0,IF';
      
      array_push($options, ...array_map(function($ord, $hex) {
        return sprintf('AREA:%sdir%s:%s', strtolower($ord), $hex, $ord);
      }, array_keys(CONFIG['chart']['o_color']), CONFIG['chart']['o_color']));

   }
  
  $graphObj->setOptions($options);
  $graphObj->save();
}


function generateSeries($graphs, $length = '1d') {
  foreach ($graphs as $type => $params) {
    createGraph("{$length}_{$type}", $type, "end-$length", 0, ...$params);
  }
}