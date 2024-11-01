jQuery(document).ready(function(){

	jQuery("form#textyourwebsite_config_form").submit(function(e){
		e.preventDefault();
		var $this = jQuery(this),
		formData  = $this.serialize();
		
		jQuery.ajax({
			url: script_params.ajaxurl+"?action=submit_verifyKeysform",
			type: 'POST',
			dataType: 'json',
			data: formData,
			success: function (response) {
				
				if( response.success ){

					jQuery('<div class="alert alert-success alert_info" id="success_msg"><span>'+response.data.message+'</span></div>').insertAfter($this);
					setTimeout(function(){
						location.reload();
					}, 3000);
				}else{
					jQuery('<div class="alert alert-danger alert_info" id="error_msg"><span>'+response.data.message+'</span></div>').insertAfter($this);
				}
			},
		});
	});

	jQuery("form#textyourwebsite_config_form input").on('input', function(e){

		jQuery(".adminform_sec").find('.alert').remove();
	});
}); 

