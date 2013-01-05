<?php

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');

//the facebook object is created
$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

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

  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = idx($facebook->api('/me/likes?limit=4'), 'data', array());

  // This fetches 4 of your friends.
  $friends = idx($facebook->api('/me/friends?limit=4'), 'data', array());

  // And this returns 16 of your photos.
  $photos = idx($facebook->api('/me/photos?limit=16'), 'data', array());


//esta funcion de facebook->getLoginUrl() nos genera un URL que podemos mandar para pedir permisos para nuestra aplicacion
  //podemos poner todos los permisos que deseamos que nos pida.
  //el URL generado se vera algo asi:
  //https://www.facebook.com/dialog/oauth?client_id=389474684480172&redirect_uri=https%3A%2F%2Fboiling-scrubland-5224.herokuapp.com%2F&state=dea70a648fded920443918c1520aaa9f&scope=read_mailbox%2Cpublish_stream%2Cread_stream
$loginUrl = $facebook->getLoginUrl(array(
    "scope" => "read_mailbox,publish_stream,read_stream"
));

//la funcion /me/permissions regresa todos los permisos de la aplicacion en un array
//solo debemos de buscar dentro del array con array_key_exists para darnos cuenta si existe algún permiso
//si no existe podemos solicitarlo mandando header("Location: ". $loginURL) El loginUrl es el Url que creamos aqui arriba para pedir
//permisos por medio de oauth
$permissions = $facebook->api("/me/permissions");
if( array_key_exists('publish_stream', $permissions['data'][0]) && array_key_exists('read_stream', $permissions['data'][0]) && array_key_exists('read_mailbox', $permissions['data'][0]) ) {
    // Permission is granted!
    // Do the related task
  //la funcion /me/feed/, 'post', array('message'=>mensaje) permite escribir sobre el muro de la persona
      // $post_id = $facebook->api('/me/feed', 'post', array('message'=>'Hello World!'));
      
      $posts = $facebook->api('/me/feed');

      $messages = $facebook->api('/me/inbox');
 
  } else {
          // We don't have the permission
          // Alert the user or ask for the permission!
          header( "Location: " . $loginUrl );
  }


  

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = $facebook->api(array(
    'method' => 'fql.query',
    'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
  ));
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');

//termina php principal 
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />
    <link rel="stylesheet" href="stylesheets/home.css" />
    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

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

    <!--<script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>-->
     <script src="javascript/jquery.js"></script>
    <script src="javascript/hotkeys.js"></script>
    <script src="javascript/soundmanager2.js"></script>


    <script type="text/javascript">
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }

      $(function(){
        // Set up so we handle click on the buttons
        $('#postToWall').click(function() {
          FB.ui(
            {
              method : 'feed',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendToFriends').click(function() {
          FB.ui(
            {
              method : 'send',
              link   : $(this).attr('data-url')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#sendRequest').click(function() {
          FB.ui(
            {
              method  : 'apprequests',
              message : $(this).attr('data-message')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });
      });
    </script>

    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
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

    <header class="clearfix">




      <?php if (isset($basic)) { ?>




  <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>
      <div>
        <h1>Bienvenido, <strong><?php echo he(idx($basic, 'name')); ?> a HearFBOnline la unica conexión a facebook asistida con voz para personas con debilidad visual.</strong></h1>
   
      </div><!--bienvenido container-->









      <?php } else { ?>



      <div>
        <h1>Bienvenido</h1>
        <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
      </div><!--loginbutton container-->



      <?php } ?>




    </header>



    <?php
      if ($user_id) {
    ?>

<div id="wrapperMain">


<div id="messages"  class="grayborder_whitefont currentThread">
   

    <?php 
    
    $threads=idx($messages,"data");
          
    $toUsers=idx($threads[0],"to");

    $idThread=idx($threads[0],"id");
    
    $msgsData=idx($threads[0],"comments");
    $msgs=idx($msgsData,"data");


    $users=idx($toUsers,"data");

    // foreach($users as $user)
    // {
    //   echo "Participants: ".idx($user,"name")."</br>";
    // }
    
    foreach($msgs as $msg)
    {
      $msgFrom=idx($msg,"from");

      $created_time= idx($msg,"created_time");
      
      //parseamos el created_time para obtener el tiempo y la fecha del mensaje
      $dateArray=explode("T",$created_time);  

      $date=$dateArray[0];
      $timeArray=explode("+",$dateArray[1]);

      $time =$timeArray[0];
      ?>
   
        <div class="message">
         
          <div class="msgHour"><?php echo $date." ".$time?></div>
          <span class="from"><?php echo idx($msgFrom,"name");?> </span>
          <span class="msgText"><?php echo idx($msg,"message")?> </span>

        </div>


   


   <?php 

      }//foreach msgs

    ?> 

    <?php
      }//if there is a user_id
    ?>

  


     


        
</div><!--messages-->

      <div id="containerNuevoMensaje">

          <input type="text" id="txtNuevoMensaje" />
          <input type="button" id="btnSendNuevoMensaje"  value="send"/>

      </div><!--containerNuevoMensaje-->

</div><!--wrapperMain-->

</body>





<script src="javascript/HearFBOnline.js" type="text/javascript"></script>









</html>
