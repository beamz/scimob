/*!
 * Metro PreLoader v1.0
 * Copyright 2013 Alireza Sheikholmolouki
 * MyWebsite : http://AlirezaDesigner.com/
 * E-mail : alireza.sheikholmolouki@gmail.com
 * Find me on Facebook : facebook.com/Alireza29675
 *
 * Date: Sun Jan 06 2013
 */
(function(){
	var ie = false;
	if (navigator.appName == 'Microsoft Internet Explorer'){
		var ua = navigator.userAgent;
		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null) ie = parseFloat( RegExp.$1 );
		if(ie != false) ie=true;
	}
	var container, center, logo, crazyloader, winW = 1024, winH = 768, addedPreloader = 0;//Local Varibales
	var el = function(element){return document.getElementById(element);};
	var Options = { //Our Default Options
		BGcolor : "#2C5E91",
		logoUrl : "",
		crazyloaderSize : "40px",
		logoWidth : "",
		logoHeight : "",
		crazyloaderTopSpace : "20px",
		useCssAnimation : "Yes",
		multipleBGColors : "No",
		delay : 2000
	};
	var mpl = {
		setup : function(setupobj){
			for(var key in setupobj) Options[key]=setupobj[key];
		},
		run : function(){
			if (typeof jQuery != 'undefined'){ $(document).ready(function(){mpl_run()}); }
			else{
				if(document.readyState=='interactive' | document.readyState=='complete') mpl_run();
				else document.onreadystatechange = function(){ if(document.readyState=='interactive') mpl_run(); }
			}
		},
		close : function(){
			if(Options.useCssAnimation.toUpperCase()=="NO") container.style.display="none";
			else{
				container.style.opacity=0;
				if(!ie){document.getElementById("MetroPreLoader_Center").style.marginTop = "50px";}
				document.getElementById("MetroPreLoader_Center").style.opacity = 0;
				setTimeout(function(){
					document.getElementById("MetroPreLoader_Container").style.display="none";
					if(!ie){document.getElementById("MetroPreLoader_Center").style.marginTop = "40px";}
					document.getElementById("MetroPreLoader_Center").style.opacity = 1;
				},1000);
			}
		}
	};
	var firstOptimize = function(){
		var preloader = "";
		if(addedPreloader==0){preloader = '<div id="MetroPreLoader_Container" class="mplTranslator"><div id="MetroPreLoader_Center" class="mplTranslator"><img id="MetroPreLoader_logo" src="" /><img id="MetroPreLoader_crazyloader" src="../img/loader_white.gif" /></div></div>';addedPreloader=1;}
		document.getElementsByTagName('body')[0].innerHTML = document.getElementsByTagName('body')[0].innerHTML + preloader;
		container = el('MetroPreLoader_Container');
		container.style.display="block";
		center = el('MetroPreLoader_Center');
		logo = el('MetroPreLoader_logo');
		crazyloader = el('MetroPreLoader_crazyloader');
	};
	var Optimize = function(){
		if (document.body && document.body.offsetWidth) {
			winW = document.body.offsetWidth;
			winH = document.body.offsetHeight;
		}
		if (document.compatMode=='CSS1Compat' && document.documentElement && document.documentElement.offsetWidth ) {
			winW = document.documentElement.offsetWidth;
			winH = document.documentElement.offsetHeight;
		}
		if (window.innerWidth && window.innerHeight) {
			winW = window.innerWidth;
			winH = window.innerHeight;
		}
		center.style.marginTop = ( ( winH - center.offsetHeight ) / 2 ) + "px";
		if(ie){
			container.style.position = "absolute";
			container.style.top = "0px";
			container.style.left = "0px";
			container.style.width = winW;
			container.style.height = winH;
		}
	};
	var mpl_run = function(){
		firstOptimize();
		if( Object.prototype.toString.call( Options.multipleBGColors ) !== '[object Array]' ){
			if(Options.multipleBGColors.toUpperCase()=="NO" || Options.multipleBGColors=="") container.style.background = Options.BGcolor;
		}
		else{
			if(!ie) { if(Options.multipleBGColors.indexOf(Options.BGcolor) == -1) Options.multipleBGColors.push(Options.BGcolor); }
			container.style.background = Options.multipleBGColors[ Math.floor( Math.random() * Options.multipleBGColors.length ) ]
		}
		logo.src=Options.logoUrl;
		if(!ie){ window.addEventListener('onresize', Optimize); window.addEventListener('resize', Optimize); }
		else{window.onresize = Optimize;}
		crazyloader.style.width = Options.crazyloaderSize;
		crazyloader.style.height = Options.crazyloaderSize;
		crazyloader.style.marginTop = Options.crazyloaderTopSpace;
		if(Options.logoWidth != "") logo.style.width=Options.logoWidth;
		if(Options.logoHeight != ""){	logo.style.height=Options.logoHeight; Optimize();	}
		if(Options.useCssAnimation.toUpperCase()=="NO"){
			container.className="";
			center.className="";
		}
		container.style.opacity=1;
		window.onload = function(){
			if(Options.delay=="Unlimited"){}
			else{
				if(Options.useCssAnimation.toUpperCase()=="NO") container.style.display="none";
				else{
					setTimeout(function(){
						document.getElementById("MetroPreLoader_Container").style.opacity=0;
						if(!ie){document.getElementById("MetroPreLoader_Center").style.marginTop = "50px";}
						document.getElementById("MetroPreLoader_Center").style.opacity = 0;
						setTimeout(function(){
							document.getElementById("MetroPreLoader_Container").style.display="none";
							if(!ie){document.getElementById("MetroPreLoader_Center").style.marginTop = "40px";}
							document.getElementById("MetroPreLoader_Center").style.opacity = 1;
						},1000);
					},Options.delay);
				}
			}
		};
	};
	window.MetroPreLoader=mpl;
})();