<?php

// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
//if the first 8 chars of the URL are different from https and the server is not local
//then we force the browser to go to a https URL
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');
//This provides the facebook php sdk
require_once('sdk/src/facebook.php');

//the facebook object is created
$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),//these credentials are taken from the server environment variables of heroku
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

//this looks to see if there is still a user active.
$user_id = $facebook->getUser();
if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }

//esta funcion de facebook->getLoginUrl() nos genera un URL que podemos mandar para pedir permisos para nuestra aplicacion
  //podemos poner todos los permisos que deseamos que nos pida.
  //el URL generado se vera algo asi:
  //https://www.facebook.com/dialog/oauth?client_id=389474684480172&redirect_uri=https%3A%2F%2Fboiling-scrubland-5224.herokuapp.com%2F&state=dea70a648fded920443918c1520aaa9f&scope=read_mailbox%2Cpublish_stream%2Cread_stream
$loginUrl = $facebook->getLoginUrl(array(
    "scope" => "read_mailbox,publish_stream,read_stream"
));


// //la funcion /me/permissions regresa todos los permisos de la aplicacion en un array
// //solo debemos de buscar dentro del array con array_key_exists para darnos cuenta si existe algún permiso
// //si no existe podemos solicitarlo mandando header("Location: ". $loginURL) El loginUrl es el Url que creamos aqui arriba para pedir
// //permisos por medio de oauth
// $permissions = $facebook->api("/me/permissions");
// if( array_key_exists('publish_stream', $permissions['data'][0]) && array_key_exists('read_stream', $permissions['data'][0]) && array_key_exists('read_mailbox', $permissions['data'][0]) ) {
//     // Permission is granted!
//     // Do the related task
//   //la funcion /me/feed/, 'post', array('message'=>mensaje) permite escribir sobre el muro de la persona
//       // $post_id = $facebook->api('/me/feed', 'post', array('message'=>'Hello World!'));
      
//       $messages = $facebook->api('/me/inbox');//we obtain the inbox of the messages
 
//   } else {
//           // We don't have the permission
//           // Alert the user or ask for the permission!
//           header( "Location: " . $loginUrl );
//   }



// 	// Fetch the basic info of the app that they are using
// 	$app_info = $facebook->api('/'. AppInfo::appID());

// 	$app_name = idx($app_info, 'name', '');
?>








<html>
	<head>
		<title>HearFB</title>
		<script src="javascript/jquery.js"></script>
		<script src="javascript/hotkeys.js"></script>
		<script src="javascript/soundmanager2.js"></script>
		
		<link rel="stylesheet" href="stylesheets/home.css" />


		<!-- These are Open Graph tags.  They add meta data to your  -->
	    <!-- site that facebook uses when your content is shared     -->
	    <!-- over facebook.  You should fill these tags in with      -->
	    <!-- your data.  To learn more about Open Graph, visit       -->
	    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->	
	    <meta property="og:title" content="<?php echo he($app_name); ?>" />
	    <meta property="og:type" content="website" />
	    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
	    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
	    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
	    <meta property="og:description" content="Facebook accessibility helper for the visually impaireds" />
	    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />


	</head>

<body class="blackBG_whitefont">
<div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
	</script>






	
	<div id="wrapperMain">
			
		<div id="HearFBOnlineTitle">
			HearFBOnline<div>Facebook for the visually impaired</div>
		</div><!--HearFBOnlineTitle-->




		
		<div id="threadsContainer">
			<div id="threadsScroller">
					<div class="thread">
						<div class="participantUsers">
							Angel Duran, Rafa Diaz
						</div><!--participantUsers-->

						<div   class="grayborder_whitefont messages">
						<!-- <div id="vScrollMessages"> -->
							
							<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>

									<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>

									<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>
						</div><!--messages-->

					</div><!--thread-->


					<div class="thread">

						<div class="participantUsers">
							Angel Duran, Rafa Diaz
						</div><!--participantUsers-->

						<div   class="grayborder_whitefont messages">
							<!-- <div id="vScrollMessages"> -->
								
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>

									<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>

									<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">Rafa Diaz</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato? Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres venir a jugar un rato?</span>

								</div>
								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">xD, nada nada y t&uacute;?</span>

								</div>

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Hola que haces? que me platicas? yo estoy bien, jugando al fifa 2012 pero ya se acabo, no quieres ve</span>

								</div>
								

								<div class="message">
									<div class="msgHour">2012-12-31 04:47:49</div>
									<span class="from">angel duran</span>
									<span class="msgText">Entonces estaba sentado frente a un arbol que me dijo que lo siguiera pero no le quize hacer caso porque me ve&iacute;a raro</span>

								</div>
					</div><!--messages-->
				</div><!--thread-->
			</div><!--threadsScroller-->
		</div><!--threadsContainer-->
		<div id="containerNuevoMensaje">

			<input type="text" id="txtNuevoMensaje" />
			<input type="button" id="btnSendNuevoMensaje"  value="send"/>

		</div><!--containerNuevoMensaje-->

	</div><!--wrapperMain-->

</body>





<script src="javascript/HearFBOnline.js" type="text/javascript"></script>
</html>