$.noConflict();
jQuery( document ).ready(function() {
  $( ".div-image-left" ).click(function() {
            $( this ).toggleClass( "ctive" );

            var x = document.getElementById("auto");
            var y = window.getComputedStyle( x ,null).getPropertyValue('background-color');
            var puntos = 0;  
          


            var capas=document.getElementsByTagName('div');
            for (i=0;i<capas.length;i++){
              var zq = window.getComputedStyle( capas[i] ,null).getPropertyValue('background-color');
               if(zq == "rgb(246, 112, 14)"){
                      puntos = puntos+10;
                } 
            }
            if(puntos > 100){
                puntos = 100;
            }
            document.getElementById("mytext").value = puntos;
  });
});