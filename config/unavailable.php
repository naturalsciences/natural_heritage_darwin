<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php $path = preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : '')) ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="title" content="Darwin" />
<meta name="robots" content="index, follow" />
<meta name="description" content="Darwin project" />
<meta name="keywords" content="darwin, project" />
<meta name="language" content="en" />
<title>symfony project</title>

<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/reset.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/menu_pub.css" />
</head>
<body>
<div class="dw_logo">
  <a href="/"><img src="/images/photo_bckg.jpg"/></a>
</div>
<div class="lyt_content">
  <div class="sfTMessageContainer sfTAlert">
    <img alt="page not found" class="sfTMessageIcon" src="<?php echo $path ?>/sf/sf_default/images/icons/tools48.png" height="48" width="48" />
    <div class="sfTMessageWrap">
      <h1>The Website is under maintenance.</h1>
      <h5>Please try again in a few seconds...</h5>
    </div>
  </div>

  <dl class="sfTMessageInfo">
    <dt>What's next</dt>
    <dd>
      <ul class="sfTIconList">
        <li class="sfTReloadMessage"><a href="javascript:window.location.reload()">Try again: Reload Page</a></li>
      </ul>
    </dd>
  </dl>
</div>
</body>
</html>
