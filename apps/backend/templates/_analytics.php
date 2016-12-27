<script type="text/javascript">

/*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo __(sfConfig::get('dw_analytics_code', ''));?>']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setCustomVar', 1, 'UserType', '<?php echo $sf_user->getDbUserType();?>', 3 ]);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
*/

    (function(i,s,o,g,r,a,m){
        i['GoogleAnalyticsObject']=r;
        i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)
        },i[r].l=1*new Date();
        a=s.createElement(o),m=s.getElementsByTagName(o)[0];
        a.async=1;
        a.src=g;
        m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-89067518-3', 'auto');

    ga('send', 'pageview');

</script>
