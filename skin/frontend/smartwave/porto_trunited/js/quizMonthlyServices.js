

jQuery( document ).ready(function() {
jQuery( ".div-image-left" ).click(function() {
          jQuery( this ).toggleClass( "ctive" );
          //var puntos = 0;    
          var count = jQuery('.ctive').length;
          var puntos = count * 20;
          if(puntos > 100){
            puntos = 100;
          }
          //document.getElementById("#mytext").value() = count;
          jQuery("#mytext").val(puntos);
          //alert(count);
});

});