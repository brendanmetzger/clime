<?php

define('CONFIG', parse_ini_file('../bin/config.ini', true));

function createAutoGraph($filename, $dataItem, $label, $title, $start, $lower, $upper, $trend, $autoscale = true) {
  $graphObj = new RRDGraph("../data/charts/{$filename}.png");

  $options = [
    "--start" => $start, // --start
    "--end" => 'now',  // --end
    '--width' => CONFIG['chart']['width'],
    '--height' => CONFIG['chart']['height'],
    '--vertical-label' => $label,
    '--title' => $title,
    '--alt-y-grid',
    
  ];
  
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
      $options[] = 'AREA:ndir#0000FF:N';    # Blue
      $options[] = 'AREA:nedir#1E90FF:NE';  # DodgerBlue
      $options[] = 'AREA:edir#00FFFF:E';    # Cyan
      $options[] = 'AREA:sedir#00FF00:SE';  # Lime
      $options[] = 'AREA:sdir#FFFF00:S';    # Yellow
      $options[] = 'AREA:swdir#FF8C00:SW';  # DarkOrange
      $options[] = 'AREA:wdir#FF0000:W';    # Red
      $options[] = 'AREA:nwdir#FF00FF:NW';  # Magenta
   }
  
  $graphObj->setOptions($options);
  $graphObj->save();
}




function generateDayGraphs() {
    createAutoGraph('1d_windspeedmph', 'windspeedmph', 'miles per hour', 'Sustained Wind', 'now-1d', 0, 0, 0);
    createAutoGraph('1d_tempf', 'tempf', 'degrees Fahrenheit', 'Temperature', 'now-1d', 0, 0, 0);
    createAutoGraph('1d_pressure', 'pressure', 'inches Hg', 'Barometric Pressure', 'now-1d', 0, 0, 0);
    createAutoGraph('1d_humidity', 'humidity', 'percent', 'Relative Humidity', 'now-1d', 0, 0, 0);
    createAutoGraph('1d_rainin', 'rainin', 'inches', 'Rain Fall', 'now-1d', 0, 0, 0, false);
}

function generateLongGraphs() {

    # 10 day long graphs
    createAutoGraph('10d_windspeedmph', 'windspeedmph', 'miles per hour', 'Sustained Wind', 'end-10days', 0, 0, 0);
    createAutoGraph('10d_tempf', 'tempf', 'degrees Fahrenheit', 'Temperature', 'end-10days',0, 0, 0);
    createAutoGraph('10d_pressure', 'pressure', 'inches Hg', 'Barometric Pressure', 'end-10days',0, 0, 0);
    createAutoGraph('10d_humidity', 'humidity', 'percent', 'Relative Humidity', 'end-10days', 0, 0, 0);
    createAutoGraph('10d_rainin', 'rainin', 'inches', 'Rain Fall', 'end-10days', 0, 0, 0, false);

    # 3 month long graphs
    createAutoGraph('3m_windspeedmph', 'windspeedmph', 'miles per hour', 'Sustained Wind', 'end-3months', 0, 0, 2);
    createAutoGraph('3m_tempf', 'tempf', 'degrees Fahrenheit', 'Temperature', 'end-3months',0, 0, 2);
    createAutoGraph('3m_pressure', 'pressure', 'inches Hg', 'Barometric Pressure', 'end-3months', 0, 0, 2);
    createAutoGraph('3m_humidity', 'humidity', 'percent', 'Relative Humidity', 'end-3months', 0, 0, 2);
    createAutoGraph('3m_rainin', 'rainin', 'inches', 'Rain Fall', 'end-3months', 0, 0, 0, false);

    # 12 month long graphs
    createAutoGraph('12m_windspeedmph', 'windspeedmph', 'miles per hour', 'Sustained Wind', 'end-12months', 0, 0, 1);
    createAutoGraph('12m_tempf', 'tempf', 'degrees Fahrenheit', 'Temperature', 'end-12months', 0, 0, 1);
    createAutoGraph('12m_pressure', 'pressure', 'inches Hg', 'Barometric Pressure', 'end-12months', 0, 0, 1);
    createAutoGraph('12m_humidity', 'humidity', 'percent', 'Relative Humidity', 'end-12months', 0, 0, 1);
    createAutoGraph('12m_rainin', 'rainin', 'inches', 'Rain Fall', 'end-12months', 0, 0, 0);
}

generateDayGraphs();
generateLongGraphs();