/*
jQuery( document ).ready(function() {
jQuery( ".div-image-left" ).click(function() {
          jQuery( this ).toggleClass( "ctive" );

          var x = document.getElementById("auto");
          var y = window.getComputedStyle( x ,null).getPropertyValue('background-color');
          var puntos = 0;  
        


          var capas=document.getElementsByTagName('div');
          for (i=0;i<capas.length;i++){
            var zq = window.getComputedStyle( capas[i] ,null).getPropertyValue('background-color');
            //alert('color: ' + zq); 
             //if(zq == "rgb(246, 112, 14)"){
              if(zq == "rgb(241, 91, 32)"){
                    puntos = puntos+10;
              } 
          }
          if(puntos > 100){
              puntos = 100;
          }/*
          document.getElementById("mytext").value = puntos;
          puntos = jQuery('.ctive').length * 10;
          document.getElementById("mytext").value = puntos;*/
          /*var count = jQuery('.ctive').length;
          alert(count);/*
});

});*/


jQuery( document ).ready(function() {
jQuery( ".div-image-left" ).click(function() {
          jQuery( this ).toggleClass( "ctive" );
          //var puntos = 0;    
          var count = jQuery('.ctive').length;
          var puntos = count * 10;
          
          if(count > 9){
            puntos = 90 + (count - 9);
          }
          
          if(puntos > 100){
            puntos = 100;
          }
          //document.getElementById("#mytext").value() = count;
          jQuery("#mytext").val(puntos);
          //alert(count);
});

});