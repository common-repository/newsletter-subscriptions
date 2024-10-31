// JavaScript Document

//admin js

// on check #newssubcriber-all-checkboxes", select all check boxes
jQuery(document).ready(function(){
	var newValue;
	jQuery('#newssubcriber-action-form').change(function () {
		 newValue = jQuery('#newssubcriber-action-form').val();
			jQuery('#newssubcriber-the-do-action').val(newValue);
    });
	
	jQuery('#newssubcriber-action-form1').change(function () {
		 newValue = jQuery('#newssubcriber-action-form1').val();
			jQuery('#newssubcriber-the-do-action').val(newValue);
    });
	
	
	jQuery('#newssubcriber-all-checkboxes').click(function () {
        jQuery('.all-checkable').attr('checked', this.checked);
    });
// on change update the form with action
    
	
	jQuery('#newssubcriber-perform-action').click(function(){

			jQuery('#newssubcriber-actions-form').submit();

});
	
		jQuery('#newssubcriber-perform-action1').click(function(){

			jQuery('#newssubcriber-actions-form').submit();

});
	
	

});


// JavaScript Document
jQuery(document).ready(function(){
	jQuery('#newssubcriber-checkbox').click(function(){	
		if(jQuery('#newssubcriber-checkbox').is(':checked')){
			jQuery('#newssubcriber-submit-button').removeAttr('disabled');
		}
		else{
			jQuery('#newssubcriber-submit-button').attr('disabled','disabled');
		}
		
	});
	// js for tab 
	jQuery(".nav-tab-wrapper.woo-nav-tab-wrapper.news-subcription-data > a").click(function(e){

	e.preventDefault();
	jQuery(".is-dismissible").hide();
	var tab = jQuery(this).attr('href');
	var actived_nav = jQuery('.nav-tab-wrapper > a.nav-tab-active');
	
	for(var i=1; i<=2; i++){
		
		var ctab = "#tab"+i
		if(tab == ctab){
			
			jQuery(ctab).show();
			jQuery(this).addClass('nav-tab-active');
			jQuery("#token_type").val(ctab);
		}else{
			
			actived_nav.removeClass('nav-tab-active');
			jQuery(ctab).hide();
		}
	}
})
});

