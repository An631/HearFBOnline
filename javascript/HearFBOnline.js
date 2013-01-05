//script para poder mandar leer texto en pantalla y acciones realizadas

var speak= new Audio(); 
var arrayTextToSpeak=new Array();

var languages=new Array();
languages[0]="es";
languages[1]="en";

var language=0;

var notyet=0;

var smIsReady=false;

soundManager.setup({
  url: 'javascript/soundmanager2/swf',
  preferFlash: false,
  flashVersion: 9,
  onready: function() {
    // console.log('SM2 ready!');
    smIsReady=true;
  },
  ontimeout: function() {
    // console.log('SM2 init failed!');
    alert("todo cayo sobre de mi");
  },
  defaultOptions: {
    // set global default volume for all sound objects
    volume: 50
  }
});//soundmanager Setup






$(document).ready(function(){

//comandos de inicialización
//*****************************************************************************
$("#txtNuevoMensaje").focus();
//aqui va el metodo que pone currentThread class a la conversación activa
$("#wrapperMain .messages:first-child").addClass("currentThread");
$(".currentThread .message:last-child").addClass("selectedMsg");
scrollMessages();






//*****************************************************************************



    //action listeners and handlers go in this area:
    //***********************************************************************************

    //used to change language
    $(document).bind('keyup','Ctrl+Shift+l', function(e){
    
    if(notyet===0)
    {
      if(language==1)
      {
        language=0;
        read("Lenguaje cambiado a español");
      }
      else
      {
        language=1;
        read("Language changed to english");
      }

      notyet=1;
      setTimeout('clearTimer()', 100);
      } 
    });


 //used to read date of current selected message
    $(document).bind('keyup','Ctrl+Shift+k', function(e){
          if(notyet===0)
          {
            //obtenemos la hora y día impresa en  el mensaje seleccionado
            var currentMsgHour=$(".currentThread .selectedMsg .msgHour").html();

            //separamos la hora y la fecha para poder enviarlos al metodo readMsgDate( por separado
            var currentMsgHourArray=currentMsgHour.split(" ");
            
            if(smIsReady)
              readMsgDate(currentMsgHourArray[1],currentMsgHourArray[0]);
            
            notyet=1;
            setTimeout('clearTimer()', 100);
          } 
    });



  $(document).bind('keydown','UP', function(e){
     
     		var currentMsg=$(".currentThread .selectedMsg");
	     	var prevMsg=$(".currentThread .selectedMsg").prev();
	     	
	     	//revisa que el mesaje seleccionado no sea el ultimo en la lista
	     	if(!$(currentMsg).is(':first-child') && (notyet===0))
	     	{
	     		//cambiamos el mensaje selecionado
		     	currentMsg.attr("class","message");
		     	prevMsg.attr("class","message selectedMsg"); 		
		     	
		     	//obtenemos la info del msg para el sintetizador de voz
		     	var from=$(".currentThread .selectedMsg .from").html();
		     	var msgText=$(".currentThread .selectedMsg .msgText").html();
		     	var msgHour=$(".currentThread .selectedMsg .msgHour").html();

          if(smIsReady)
		     	//mandamos leer el mensaje al sintetizador de google
		     	readMessage(from,msgText,msgHour);

		     	scrollMessages();
		     	notyet=1;
		     	setTimeout('clearTimer()', 100);
	     	}	

     });


     $(document).bind('keydown','DOWN', function(e){
     

     		var currentMsg=$(".currentThread .selectedMsg");
          var nextMsg=$(".currentThread .selectedMsg").next();
	     	
	     	//revisa que el mesaje seleccionado no sea el ultimo en la lista
	     	if(!$(currentMsg).is(':last-child') && (notyet===0))
	     	{
          

		     	currentMsg.attr("class","message");
		     	nextMsg.attr("class","message selectedMsg"); 		
		     	scrollMessages();

				//obtenemos la info del msg para el sintetizador de voz
		     	var from=$(".currentThread .selectedMsg .from").html();
		     	var msgText=$(".currentThread .selectedMsg .msgText").html();
		     	var msgHour=$(".currentThread .selectedMsg .msgHour").html();

          if(smIsReady)
		     	//mandamos leer el mensaje al sintetizador de google
		     	readMessage(from,msgText,msgHour);

		     	scrollMessages();
			  	notyet=1;
		     	setTimeout('clearTimer()', 100);
	     	}	
     });


      //if I click over a text it should change the selectedMsg class and say the message on it
    $(".message").click(function(e){
      

        if(notyet===0)
        {
          var currentMsg=$(".currentThread .selectedMsg");

          currentMsg.attr("class","message");
          $(this).attr("class","message selectedMsg");    
          scrollMessages();

        //obtenemos la info del msg para el sintetizador de voz
          var from=$(".currentThread .selectedMsg .from").html();
          var msgText=$(".currentThread .selectedMsg .msgText").html();
          var msgHour=$(".currentThread .selectedMsg .msgHour").html();

          if(smIsReady)
          //mandamos leer el mensaje al sintetizador de google
          readMessage(from,msgText,msgHour);

          scrollMessages();
          notyet=1;
          setTimeout('clearTimer()', 100);
        } 

      
      
     
    });//message click


    //this method is used for the button to read
    $("#btnSendNuevoMensaje").click(function(e){

      var texto=$("#txtNuevoMensaje").val();

      
      if(smIsReady)
      read(texto);
    });




    //************************************************************************************

      

});//document.ready

//********************************************************************************************************************************
//UTILITIES FUNCTIONS:
function clearTimer()
{
	
notyet=0;
}



function scrollMessages()
{

	var container=$(".currentThread");
	var scrollTo=$(".selectedMsg");

	container.animate({
		
		
	    scrollTop: (scrollTo.offset().top - container.offset().top + container.scrollTop())

	},{duration:'10'});
}//scroll messages




//esta función lee exclusivamente mensajes utilizando la funcion read()
function readMessage(from, message)
{

  
  message=modernDictionaryTranslate(message);

    //se debe de separar el texto en pedazos de 100 caracteres para que google los acepte.

    // if (message.length>50) 
    // {
      var piecesCounter=0;
      for(i=0;i<message.length;(i=i+50))
      {
        var pieceOfMsg=message.substring(i,i+50);
        if(i===0)
          pieceOfMsg=from+" dijo "+pieceOfMsg;
        arrayTextToSpeak[piecesCounter]="http://translate.google.com/translate_tts?ie=UTF-8&q="+encodeURI(pieceOfMsg)+"&tl="+languages[language]+"&total=1&idx=0prev=input";
        piecesCounter=piecesCounter+1;
        // alert(arrayTextToSpeak[piecesCounter]);
      }

      readArrayOfSounds(0,arrayTextToSpeak);

    // }
    // else
    // {
    //   var texto=from+" dijo: "+message;
    //   read(texto);
    // }//else if message.length>100


}//readMessage

//funcion que lee la hora que se le envíe
function readMsgDate(hour,date)
{
  var texto="Enviado a las " +hour+" el "+date;
  read(texto);
}


//this method reads an array of sounds one after another
function readArrayOfSounds(indice,arraySounds)
{
  // alert(arrayTextToSpeak[indice]+" indice: "+indice);
  if(indice<arraySounds.length)
  {
     stopAllSounds();//detenemos todos los 
    soundManager.createSound({
        id:'a'+indice,
        url:arraySounds[indice]
        });
    
        soundManager.play('a'+indice,{
        multiShotEvents:true,
        onfinish:function(){
          soundManager.destroySound('a'+indice);
          stopAllSounds();
         if((indice+1)<arraySounds.length)
          readArrayOfSounds(indice+1,arraySounds);
        }//onfinish

    });//play

  }//if indice limits
}


//recieves a text string to translate it into speech and read it out loud.
function read(txt){
	
  //this part is going to be sweet because I have to separate the txt string into chunks of 100 chars in order for the google tts service
  //to provide me with texts longer than 100 chars translated to voice.
    play_sound("http://translate.google.com/translate_tts?ie=UTF-8&q="+encodeURI(txt)+"&tl="+languages[language]+"&total=1&idx=0prev=input");           
}

//plays the sound sending it to google TTS service and creating a html5 sound tag to play the sound if it is suported
//if not then it will embed an object of type audio/mpeg and ask it to play
function play_sound(url){
    if(html5_audio){
      //if there is a speak object existing we make sure to stop it before sending a new one.
      
     stopAllSounds();
      // speak.pause();
      speak = new Audio(url);
        
      speak.load();
    
      speak.play();
      


    }else{
        $("#sound").remove();
        var sound = $("<embed id='sound' type='audio/mpeg' />");
        sound.attr('src', url);
        sound.attr('loop', false);
        sound.attr('hidden', true);
        sound.attr('autostart', true);
        $('body').append(sound);
    }
}

//this methods stops all sounds
function stopAllSounds()
{

  for(i = 0; i < 20 ;i=i+1)
  {
    soundManager.destroySound('a'+i);//nos deshacemos del sonido creado para que no se repita en el proximo mensaje
  }
  speak.pause();
}

//checks if html5 audio is supported by the browser
function html5_audio(){
    var a = document.createElement('audio');
    return !!(a.canPlayType && a.canPlayType('audio/mpeg;').replace(/no/, ''));
}


//this function is so sweet! because it translates young slang words like pz to pues and lmfao to laughing my f*****g a*s of. =D
function modernDictionaryTranslate(texto)
{ 
  if(language==0)
  {
    texto=texto.replace(/\bpz\b/gi,"pues");
    texto=texto.replace(/\bk\b/gi,"que");
    texto=texto.replace(/\bgad\b/gi,"Gracias a dios");
    texto=texto.replace(/\bntc\b/gi,"No te creas");
  }
  else if(language==1)
  {
    texto=texto.replace(/\blmfao\b/gi,"laughing my fucking ass of");
    texto=texto.replace(/\bk\b/gi,"que");
  }
  texto=texto.replace(/\bxD\b/gi,"me muero de risa");
  texto=texto.replace(/\b=D\b/gi,"estoy felíz");
    texto=texto.replace(/\b:D\b/gi,"sonrío");


  return texto;
}

function log(error)
{
  $("#errorlog").text(error);
}