    // Hides login div and button to SignUp by default
 jQuery('document').ready(function() {
  jQuery('#login').hide();
  jQuery('#changeSignUp').hide();
  jQuery('#changeLogin').click(function(){ 
      jQuery('#signUp').hide();
      jQuery('#login').show();
      jQuery('#changeSignUp').show();
      jQuery(this).hide();
  });

  jQuery('#changeSignUp').click(function(){ 
      jQuery('#login').hide();
      jQuery('#signUp').show();
      jQuery('#changeLogin').show();
      jQuery(this).hide();
  }); 

});