/**
 * Bootstrap Growl - Notifications popups
 * 
 * @param	string		message		message
 * @param	string		type		success / warning / danger
 */
function notify (message, type) {
	jQuery.growl({
		message: message
	},{
		type: type
	});
}

/**
 * check minlength
 * 
 * @param	object		obj		jQuery this
 */
function check_minlength (obj) {
	var minlength = obj.attr('minlength');
	var length = obj.val().length;
	
	if (length >= minlength) {
		obj.parent('.form-group').addClass('has-success').removeClass('has-error');
		obj.next('.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
	} else {
		obj.parent('.form-group').addClass('has-error').removeClass('has-success');
		obj.next('.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
	}
}

jQuery(function(){
	// input setting
	jQuery('input').each(function(){
		// set class has-feedback
		jQuery(this).parent('.form-group').addClass('has-feedback');
		
		// append span
		jQuery(this).after(jQuery('<span/>',{
			class: 'glyphicon form-control-feedback'
		}));
			
		// input minlength check
		if (jQuery(this).attr('minlength')) {
			jQuery(this).keyup(function(){
				check_minlength(jQuery(this));
			}).blur(function(){
				check_minlength(jQuery(this));
			});
		}
		
		// input required check
		if (jQuery(this).attr('required')) {
			jQuery(this).blur(function(){
				var flag = (jQuery(this).attr('minlength'))?true:false;
				var value = jQuery(this).val();
				
				if (value) {
					if (!flag) {
						jQuery(this).parent('.form-group').addClass('has-success').removeClass('has-error');
						jQuery(this).next('.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
					}
				} else {
					jQuery(this).parent('.form-group').addClass('has-error').removeClass('has-success');
					jQuery(this).next('.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
				}
			});
		}
	});
});