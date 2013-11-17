<!DOCTYPE html>
<html lang="en">
<head>
	<title>Passwd</title>
	<meta content='width=device-width, target-densitydpi=device-dpi, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, user-scalable=no' name='viewport' />	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Passwd">
    <meta name="author" content="Zill Christian">

	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<style>
		body {
			/*background: url(img/blueprint.jpg);*/
			background-color: #222;
			padding: 0px;
			width: 100%;
			height: 100%;
		}
		body h1 {
			margin-top: 30px;
			margin-bottom: 40px;
		}
		.loginBox {
			margin: 0px auto;
			margin-bottom: 40px;
		}
		input.username, input.password {
			width: 350px;
			height: 50px;
			padding-left: 10px;
			background-color: rgba(0,0,0,0.25);
			font-weight: 400;
			color: #ffffff;
			display: block;
			margin-bottom: 0px;
		}
		.knobDiv {
			margin-top: 60px;
			margin-bottom: 0px;
		}
		#btnSubmit {
			margin-top: 10px;
			width: 350px;
			height: 50px;
			background: transparent;
			background-color: transparent;
			border: none;
			border: 1px solid white;
			color: #ffffff;
			transition: color 2s, background-color 2s, font-weight 1s;
			-webkit-transition: color 1s, background-color 0.5s, font-weight 1s;
		}
		#btnSubmit:hover, #btnSubmit:focus, #btnSubmit:active {
			color: #222;
			background-color: #ffffff;
			font-weight: 300;
		}
		@media (max-width: 400px) {
			body {
				margin: 0px;
				padding: 0px;
			}
			body h1 {
				margin-top: 20px;
			}
			.knobDiv {
				margin-top: 30px;
				margin-bottom: 0px;
			}
		}
	</style>
    <!--[if IE]>
	    <link rel="stylesheet" href="font/css/font-awesome-ie7.min.css">
    	<script src="js/lib/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
	<h1 align="center">Passwd</h1>
	<form class="form" action="scripts/login.php" method="post">
	<div class="header">
		<div class="loginBox" align="center">
			<input type="text" class="input username" name="username" placeholder="Enter Username" autocomplete="off" required="required">
			<input type="password" class="input password" name="password" placeholder="Enter Password" required="required">
			<input type="submit" id="btnSubmit">
		</div>
		
		<div style="position:relative;width:280px;margin:0px auto">
            <div style="position:absolute;left:10px;top:10px">
                <input class="dial hour" data-min="0" data-max="24" data-bgColor="#333" data-fgColor="#ffec03" data-displayInput=false data-width="250" data-height="300" data-thickness=".3" data-skin="tron">
            </div>
            <div style="position:absolute;left:60px;top:60px">
                <input class="dial minute" data-min="0" data-max="60" data-bgColor="#333" data-displayInput=false data-width="150" data-height="200" data-thickness=".45">
            </div>
            <div style="position:absolute;left:110px;top:110px">
                <input class="dial second" data-min="0" data-max="60" data-bgColor="#333" data-fgColor="rgb(127, 255, 0)" data-displayInput=false data-width="50" data-height="100" data-thickness=".3">
            </div>
        </div>
	</div>
	</form>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.knob.js"></script>
	<script type="text/javascript">
		
		function clock() {
	        var $s = $(".second"),
	            $m = $(".minute"),
	            $h = $(".hour");
	            d = new Date(),
	            s = d.getSeconds(),
	            m = d.getMinutes(),
	            h = d.getHours();
	        $s.val(s).trigger("change");
	        $m.val(m).trigger("change");
	        $h.val(h).trigger("change");
	        setTimeout("clock()", 1000);
	    }
	    clock();
	    	
		$(".dial").knob({
			'release' : function (v) {  }
		});
		
		
	</script>
</body>
</html>