<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"  xmlns:svg="http://www.w3.org/2000/svg" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="application/xhtml+xml;charset=utf-8" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-title" content="Third Coast"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=3,minimum-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="Third Coast International Audio Festival" />
    
    
    <title>Weather</title>
    <script type="text/javascript">
      // <![CDATA[
      addEventListener('DOMContentLoaded', (function(s, js, cb, cache) {
        self.app = {
          include: (...a) => js.push(['src','async'].reduce((s,v,i) => {s[v]=a[i]; return s}, s.cloneNode())),
          release: (evt ) => { let f; while (f = cb.shift()) f.call(app, evt) },
          module : (k   ) => (k in cache) ? cache[k] : app.define.bind(app, k),
          define : (k, f) => (k in cache) ? cache[k] : cache[k] = f,
          mobile : (/mobile|iPhone/i).test(navigator.userAgent),
          remove : (k   ) => delete cache[k],
          prepare: (f, w) => cb.push(f),
        };
        return (evt) => {
          js.filter(s => !s.async).pop().addEventListener('load', app.release);
          js.forEach(Node.prototype.appendChild.bind(document.head));
        };
      })(document.createElement('script'), [], [], Object.create(null)));
      
      document.documentElement.classList.add(['touch','click'][+app.mobile]);
      
      // app.include('/static/kit.js', false);
      // ]]>
    </script>
    <style type="text/css" media="screen">
      /* <![CDATA[ */
      *, *:before, *:after {box-sizing: border-box;}
      :root {
        font-family: 'Courier New';
      }
      body { padding: 5vw;}
      h1, h2 { font-weight: 100;}
      img {
        max-width: 100%;
      }
      /* ]]> */
    </style>
  </head>
  <body>
    <header>
      <h1>Weather</h1>
    </header> 
    <p>more interface soon</p>
    <?php foreach (glob('charts/*.png') as $path): ?>
      <img src="<?php echo $path ?>"/>      
    <?php endforeach ?>
  </body>
</html>
