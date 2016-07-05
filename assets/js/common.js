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
	
	// csrf reset
	document.getElementById('hIframe').src = site_url+'/manana/csrf/';
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
		obj.closest('.form-group').addClass('has-success').removeClass('has-error');
		obj.next('.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
	} else {
		obj.closest('.form-group').addClass('has-error').removeClass('has-success');
		obj.next('.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
	}
}

/**
 * clickFileUpload
 * 
 * @param	{Object}	f			this.form
 * @param	{string}	model
 * @param	{numberic}	model_id
 * @param	{string}	action		refresh / function
 */
function clickFileUpload (f,model,model_id,action) {
	form = f;
	
	if (model) {
		document.forms['ffileupload'].model.value = model;
	}
	
	if (model_id) {
		document.forms['ffileupload'].model_id.value = model_id;
	} else {
		document.forms['ffileupload'].model_id.value = 0;
	}
	
	if (action) {
		document.forms['ffileupload'].action.value = action;
	} else {
		document.forms['ffileupload'].action.value = 'file_upload';
	}
	
	jQuery('#ffileupload').find('[name="file"]').click();
}

/**
 * file_upload
 * 
 * @param	{numberic}	id
 * @param	{string}	name
 * @param	{string}	path
 * @param	{numberic}	size
 * @param	{numberic}	is_image
 */
function file_upload (id,name,path,size,is_image) {
	var html = insert_html = fid = '';
	
	fid = jQuery(form).attr('id');
	console.log(id+'/'+name+'/'+path+'/'+size+'/'+is_image);
	if (is_image == "1") {
		// image
		html += '<li class="'+jQuery(form).find('ul.files.thumbnails').data('li-class')+'">';
			html += '<img src="'+path+'" alt="'+name+'" />';
			html += '<div class="btn-group btn-group-justified">';
				html += '<div class="btn-group">';
					html += '<button class="btn btn-default">Insert</button>';
				html += '</div>';
				html += '<div class="btn-group">';
					html += '<button class="btn btn-danger">Delete</button>';
				html += '</div>';
			html += '</div>';
		html += '</li>';
	} else {
		// file
		html += '<li>';
			html += '<div class="btn-group">';
				html += '<button class="btn btn-default" onclick="write_editor_html(\''+fid+'\')">'+name+'</button>';
				html += '<button class="btn btn-danger" onclick="clickFileDelete(this.form,'+id+')">Delete</button>';
			html += '</div>';
		html += '</li>';
		
		jQuery(form).find('ul.list-unstyled.files').append(html);
	}
}

/**
 * clickFileDelete
 * 
 * @param	{Object}	f
 * @param	{numberic}	id
 * @param	{string}	action
 */
function clickFileDelete (f,id,action) {
	form = f;
	
	if (confirm(read_language('system_file_delete_question'))) {
		jQuery.ajax({
			type:'post',
			dataType:'json',
			url:site_url+'/file/deleteAjax/',
			data:{
				id:id
			},
			success:function(response){
				if (response.status) {
					jQuery.cookie(prefix+'noti',response.message);
					jQuery.cookie(prefix+'noti_type','success');
					
					if (action == 'refresh') {
						document.location.href = document.location.href;
					} else {
						
					}
				} else {
					notify(response.message,'danger');
				}
			}
		});
	}
}

/**
 * read_language
 * 
 * @param	{string}	text
 * @param	{string}	file
 * @param	{string}	language
 */
function read_language (text,file,language) {
	var lang = '';
	
	if (!file) {
		file = 'common';
	}
	
	if (!language) {
		language = jQuery.cookie(prefix+'language');
	}
	
	jQuery.ajax({
		type:'post',
		dataType:'json',
		async:false,
		url:site_url+'/manana/language/',
		data:{
			text:text,
			file:file,
			language:language
		},
		success:function(response){
			if (response.lang) {
				lang = response.lang;
			}
		}
	});
	
	return lang;
}

jQuery(function(){
	// input setting
	jQuery('input[type!="hidden"]').each(function(){
		// set class has-feedback
		jQuery(this).parent('.form-group').addClass('has-feedback');
		jQuery(this).parent('.input-group').parent('.form-group').addClass('has-feedback');
		
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
						jQuery(this).closest('.form-group').addClass('has-success').removeClass('has-error');
						jQuery(this).next('.glyphicon').addClass('glyphicon-ok').removeClass('glyphicon-remove');
					}
				} else {
					jQuery(this).closest('.form-group').addClass('has-error').removeClass('has-success');
					jQuery(this).next('.glyphicon').addClass('glyphicon-remove').removeClass('glyphicon-ok');
				}
			});
		}
	});
	
	// file upload submit
	jQuery('#ffileupload').find('[name="file"]').change(function(){
		jQuery('#ffileupload').submit();
	});
	
	// textarea autosize
	autosize(jQuery('textarea'));
	
	// jQuery ajax setup
	jQuery.ajaxSetup({
		beforeSend:function(xhr,settings){
			// setting csrf
			settings.data = jQuery.param(csrf)+'&'+settings.data;
		},
		complete:function(xhr,status){
			// csrf reset
			document.getElementById('hIframe').src = site_url+'/manana/csrf/';
		}
	});
	
	// jQuery cookie default setting
	jQuery.cookie.defaults = {path:'/'};
});