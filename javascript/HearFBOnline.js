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
//le damos el tamaño necesario al contenedor de conversaciones
var threadsTotal=$("#threadsScroller > .thread").size();//aqui obtenemos el numero de threads que existen para poder darle un tamaño a su contenedor
$("#threadsScroller").css("width",((threadsTotal*980)+10)+"px");

//aqui va el metodo que pone currentThread class a la conversación activa
$("#threadsScroller .thread").first().addClass("currentThread");
$(".currentThread .message:last-child").addClass("selectedMsg");


scrollConversations();
scrollMessages();

$("#txtNuevoMensaje").focus();




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


//pressing the up arrow moves to the upper message from the current conversation
  $(document).bind('keydown','UP', function(e){
     
     		var currentMsg=$(".currentThread .selectedMsg");
	     	var prevMsg=$(".currentThread .selectedMsg").prev();
	     	
	     //not yet evita que hotkeys mande llamar varias veces a esta funcion usando un timer
	     	if(notyet===0)
	     	{
          //revisa que el mesaje seleccionado no sea el primero en la lista
          if(!$(currentMsg).is(':first-child'))
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
            //movemos el scroll bar hacia el mensaje seleccionado
  		     	scrollMessages();
            //damos de alta la bandera notyet que nos permite realizar la proxima llamada para evitar que se repita la accion varias veces en una presionada.
  		     	notyet=1;
  		     	setTimeout('clearTimer()', 100);
          }//first-child
          else//si ya estamos en el primer mensaje
          {
            //obtenemos la info del msg para el sintetizador de voz
            var from=$(".currentThread .selectedMsg .from").html();
            var msgText=$(".currentThread .selectedMsg .msgText").html();
            var msgHour=$(".currentThread .selectedMsg .msgHour").html();

            if(smIsReady)
            //mandamos leer el mensaje al sintetizador de google
            readMessage(from,msgText,msgHour);
             //damos de alta la bandera notyet que nos permite realizar la proxima llamada para evitar que se repita la accion varias veces en una presionada.
            notyet=1;
            setTimeout('clearTimer()', 100);
          }
	     	}//notyet

     });


//pressing the Ctrl+up arrow moves to the first message from the current conversation
  $(document).bind('keydown','Ctrl+UP', function(e){
     
        var currentMsg=$(".currentThread .selectedMsg");
        var firstMsg=$(".currentThread .message:first-child");
        
        //revisa que el mesaje seleccionado no sea el primero en la lista
        if(notyet===0)
        {
          //cambiamos el mensaje selecionado
          currentMsg.attr("class","message");
          firstMsg.attr("class","message selectedMsg");    
          
          //obtenemos la info del msg para el sintetizador de voz
          var from=$(".currentThread .selectedMsg .from").html();
          var msgText=$(".currentThread .selectedMsg .msgText").html();
          var msgHour=$(".currentThread .selectedMsg .msgHour").html();

          if(smIsReady)
          //mandamos leer el mensaje al sintetizador de google
          readMessage(from,msgText,msgHour);
          //movemos el scroll bar hacia el mensaje seleccionado
          scrollMessages();
          //damos de alta la bandera notyet que nos permite realizar la proxima llamada para evitar que se repita la accion varias veces en una presionada.
          notyet=1;
          setTimeout('clearTimer()', 100);
        } 

     });

//pressing the down arrow moves down 1 message in the current conversation, it will read that message too.
     $(document).bind('keydown','DOWN', function(e){
     

     		var currentMsg=$(".currentThread .selectedMsg");
          var nextMsg=$(".currentThread .selectedMsg").next();
	     	
	     	//revisa que el mensaje seleccionado no sea el ultimo en la lista
	     	if(notyet===0)
	     	{
          if(!$(currentMsg).is(':last-child'))
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

  		     
  			  	notyet=1;
  		     	setTimeout('clearTimer()', 100);
          }//last-child
          else
          {
            //obtenemos la info del msg para el sintetizador de voz
            var from=$(".currentThread .selectedMsg .from").html();
            var msgText=$(".currentThread .selectedMsg .msgText").html();
            var msgHour=$(".currentThread .selectedMsg .msgHour").html();

            if(smIsReady)
            //mandamos leer el mensaje al sintetizador de google
            readMessage(from,msgText,msgHour);

           
            notyet=1;
            setTimeout('clearTimer()', 100);
          }
	     	}//notyet
     });

//pressing the Ctrl+down arrow moves down to the last message in the current conversation, it will read that message too.
     $(document).bind('keydown','Ctrl+DOWN', function(e){
     

        var currentMsg=$(".currentThread .selectedMsg");
          var lastMsg=$(".currentThread .message:last-child");
        
        //revisa que el mensaje seleccionado no sea el ultimo en la lista
        if(notyet===0)
        {
          

          currentMsg.attr("class","message");
          lastMsg.attr("class","message selectedMsg");    
          scrollMessages();

        //obtenemos la info del msg para el sintetizador de voz
          var from=$(".currentThread .selectedMsg .from").html();
          var msgText=$(".currentThread .selectedMsg .msgText").html();
          var msgHour=$(".currentThread .selectedMsg .msgHour").html();

          if(smIsReady)
          //mandamos leer el mensaje al sintetizador de google
          readMessage(from,msgText,msgHour);

         
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

          
          notyet=1;
          setTimeout('clearTimer()', 100);
        } //if not yet==0
    });//message click

    //When pressing the left arrow the conversation will change to the previous one by changing the currentThread class from div.
    $(document).bind('keyup','LEFT',function(e){

        var currentThread=$(".currentThread");
          var prevThread=$(".currentThread").prev();

        
        //revisa que el mesaje seleccionado no sea el ultimo en la lista
        if(!$(currentThread).is(':first-child') && (notyet===0))
        {
          
          currentThread.attr("class","thread");
          prevThread.attr("class","thread currentThread");    
          scrollConversations();

        //obtenemos la info de los participantes para el sintetizador de voz
          var participants=$(".currentThread .participantUsers").html();
        

          if(smIsReady)
          //mandamos leer el mensaje al sintetizador de google
          readParticipants(participants);
          //recorre las conversaciones hasta  hasta poner en pantalla a la seleccionada
          scrollConversations();
          if($(".currentThread .selectedMsg").length===0)
          //selecciona el ultimo mensaje de la conversacion seleccionada
          $(".currentThread .message:last-child").addClass("selectedMsg");
          //pone el mensaje seleccionado en pantalla
          scrollMessages();
          notyet=1;
          setTimeout('clearTimer()', 100);
        } 

    });//left arrow

    //When pressing the right arrow the conversation will change to the next one by changing the currentThread class from div.
    $(document).bind('keyup','RIGHT',function(e){
        var currentThread=$(".currentThread");
          var nextThread=$(".currentThread").next();
          
        
        //revisa que el mesaje seleccionado no sea el ultimo en la lista
        if(!$(currentThread).is(':last-child') && (notyet===0))
        {
          
          
          currentThread.attr("class","thread");
          nextThread.attr("class","thread currentThread");    
          scrollConversations();

        //obtenemos la info de los participantes para el sintetizador de voz
          var participants=$(".currentThread .participantUsers").html();
       

          if(smIsReady)
          //mandamos leer el mensaje al sintetizador de google
          readParticipants(participants);

          //recorre las conversaciones hasta poner en pantalla a la seleccionada
          scrollConversations();
         
           if($(".currentThread .selectedMsg").length===0)
          //selecciona el ultimo mensaje de la conversacion seleccionada
          $(".currentThread .message:last-child").addClass("selectedMsg");
          //pone el mensaje seleccionado en pantalla
          scrollMessages();
          notyet=1;
          setTimeout('clearTimer()', 100);
        } 

    });//Right arrow


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


///se posiciona sobre el mensaje seleccionado 
function scrollMessages()
{

	var container=$(".currentThread .messages");
	var scrollTo=$(".currentThread .messages .selectedMsg");
  var scrollToPosition=scrollTo.offset().top - container.offset().top + container.scrollTop();
// alert("container: "+container.offset().top+" scrollTo: "+scrollTo.offset().top+" scrollTop: "+scrollToPosition);
	container.animate({
		
		
	    scrollTop: (scrollToPosition)

	});
}//scroll messages

function scrollConversations()
{
  var container =$("#threadsContainer");
  var toElement=$(".currentThread");

  container.animate({
      scrollLeft:(toElement.offset().left-container.offset().left+container.scrollLeft())
  });//animate
}//scrollConversations


//esta función lee exclusivamente mensajes utilizando la funcion read()
function readMessage(from, message)
{

  var fromSize = from.length;//we obtain the lenght of the person that wrote the msg so we might count the total sent to google tts web service

  message=modernDictionaryTranslate(message);
  arrayTextToSpeak=new Array();//reiniciamos el array que contiene lo que se quiere decir para que no se repitan los mensajes
  var pieceOfMsg="";

    if(language===0)
       pieceOfMsg=from+" dijo ";
      else if(language===1)
       pieceOfMsg=from+" said ";

var arrayOfWords=message.split(" ");

var piecesCounter=0;
 for(i=0;i<arrayOfWords.length; i++)
  {
 
      pieceOfMsg=pieceOfMsg+" "+arrayOfWords[i];
      
      if(((i+1)>=arrayOfWords.length)||(pieceOfMsg+" "+arrayOfWords[i+1]).length>=100)
      {
      
        arrayTextToSpeak[piecesCounter]="http://translate.google.com/translate_tts?ie=UTF-8&q="+encodeURI(pieceOfMsg)+"&tl="+languages[language]+"&total=1&idx=0prev=input";
        pieceOfMsg="";
        piecesCounter=piecesCounter+1;
      }
  }

    //se debe de separar el texto en pedazos de 100 caracteres para que google los acepte.
      // var piecesCounter=0;
      // for(i=0;i<message.length;(i=i+50))
      // {
      //   var pieceOfMsg=message.substring(i,i+50);
      //   if(i===0)//la primer parte del mensaje debe de decir el nombre de quien dijo ese mensaje
      //   {
      //     if(language===0)
      //     pieceOfMsg=from+" dijo "+pieceOfMsg;
      //     else if(language===1)
      //       pieceOfMsg=from+" said "+pieceOfMsg;
      //   }
      //   arrayTextToSpeak[piecesCounter]="http://translate.google.com/translate_tts?ie=UTF-8&q="+encodeURI(pieceOfMsg)+"&tl="+languages[language]+"&total=1&idx=0prev=input";
      //   piecesCounter=piecesCounter+1;
      //   // alert(arrayTextToSpeak[piecesCounter]);
      // }

      readArrayOfSounds(0,arrayTextToSpeak);

    


}//readMessage

//funcion que lee la hora que se le envíe
function readMsgDate(hour,date)
{
  if(language===0)
  var texto="Enviado a las " +hour+" el "+date;
  else if(language===1)
    var texto="Sent at " +hour+" "+date;
  read(texto);
}

//funcion que lee a los participantes de una conversación
function readParticipants(participants)
{
  if(language===0)
  var texto= "Chat con "+participants;
else if(language===1)
  var texto= "Chat with "+participants;
  read(texto);
}
//this method reads an array of sounds one after another
function readArrayOfSounds(indice,arraySounds)
{
  // alert(arrayTextToSpeak[indice]+" indice: "+indice);
  if(indice<arraySounds.length)
  {
     stopAllSounds();//detenemos todos los sonidos
    soundManager.createSound({
        id:'a'+indice,
        url:arraySounds[indice]
        });
    
        soundManager.play('a'+indice,{
        multiShotEvents:true,
        onfinish:function(){
          // soundManager.destroySound('a'+indice);
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
    soundManager.destroySound("a"+i);//nos deshacemos del sonido creado para que no se repita en el proximo mensaje
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
    texto=texto.replace(/\bbn\b/gi,"bien");
    texto=texto.replace(/\btmb\b/gi,"tambien");
    texto=texto.replace(/\bhm*\b/gi,"no me convences");
  }
  else if(language==1)
  {
    texto=texto.replace(/\blmfao\b/gi,"laughing my fucking ass of");
    texto=texto.replace(/\bk\b/gi,"que");
    texto=texto.replace(/\bya\b/gi,"you");
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