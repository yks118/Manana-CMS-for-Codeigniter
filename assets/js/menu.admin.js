/**
 * changeMenuModel
 */
function changeMenuModel () {
	var model = jQuery('#menu_model > option:selected').val();
	
	if (model == 'outpage') {
		jQuery('#menu_href').parent().removeClass('hide');
		jQuery('#menu_model_id').parent().addClass('hide');
	} else {
		jQuery('#menu_href').parent().addClass('hide');
		jQuery('#menu_model_id').parent().removeClass('hide');
		
		jQuery.ajax({
			type:'post',
			dataType:'json',
			url:site_url+'/admin/readMenuModelIdAjax/',
			data:{
				model:model
			},
			success:function(response){
				if (response.status) {
					jQuery('#menu_model_id > option').remove();
					
					jQuery.each(response.data,function(key,row){
						var html = '';
						
						if (model == 'board') {
							html += '<option value="'+row.board_config_id+'">'+row.name+'</option>';
						} else if (model == 'page') {
							html += '<option value="'+row.page_id+'">'+row.title+'</option>';
						}
						
						jQuery('#menu_model_id').append(html);
					});
				} else {
					notify(response.message,'danger');
				}
			}
		});
	}
}

/**
 * changeMenuIndex
 * 
 * 메뉴 순서 저장
 */
function changeMenuIndex () {
	jQuery.ajax({
		type:'post',
		dataType:'json',
		url:site_url+'/admin/updateMenuIndexAjax/',
		data:{
			language:jQuery.cookie(prefix+'language'),
			node:jQuery('#nestable').nestable('serialize')
		},
		success:function(response){
			if (response.status) {
				notify(response.message,'success');
			} else {
				notify(response.message,'danger');
			}
		}
	});
}

/**
 * clickMenuAdd
 * 
 * @param	{numberic}		parent_id		ci_site_menu.parent_id
 */
function clickMenuAdd (parent_id) {
	document.fwrite.menu_site_menu_id.value = 0;
	document.fwrite.menu_parent_id.value = parent_id;
	document.fwrite.menu_name.value = '';
	document.fwrite.menu_uri.value = '';
	document.fwrite.menu_href.value = '';
	
	jQuery('#fwrite').find('[name="menu_model"]').children('option').eq(0).prop('selected',true).trigger('change');
	jQuery('#fwrite').find('[name="grade[]"]').prop('checked',true);
}

/**
 * clickMenuUpdate
 * 
 * @param	{numberic}	site_menu_id	ci_site_menu.site_menu_id
 */
function clickMenuUpdate (site_menu_id) {
	var csrf_bak = JSON.stringify(csrf);
	
	jQuery.ajax({
		type:'post',
		dataType:'json',
		url:site_url+'/admin/readMenuId/',
		data:{
			id:site_menu_id
		},
		success:function(response){
			if (response.status) {
				document.fwrite.menu_site_menu_id.value = response.data.site_menu_id;
				document.fwrite.menu_parent_id.value = response.data.parent_id;
				document.fwrite.menu_name.value = response.data.name;
				document.fwrite.menu_uri.value = response.data.uri;
				document.fwrite.menu_href.value = response.data.href;
				
				jQuery('#fwrite').find('[name="menu_target"] > option[value="'+response.data.target+'"]').prop('selected',true);
				
				jQuery.each(response.data.grade,function(key,value){
					jQuery('#fwrite').find('[name="grade[]"][value="'+value+'"]').prop('checked',true);
				});
				
				var interval = setInterval(function(){
					if (JSON.stringify(csrf) != csrf_bak) {
						jQuery('#fwrite').find('[name="menu_model"] > option[value="'+response.data.model+'"]').prop('selected',true).trigger('change');
						clearInterval(interval);
						
						csrf_bak = JSON.stringify(csrf);
						interval = setInterval(function(){
							if (JSON.stringify(csrf) != csrf_bak) {
								jQuery('#fwrite').find('[name="menu_model_id"] > option[value="'+response.data.model_id+'"]').prop('selected',true);
								clearInterval(interval);
							}
						},100);
					}
				},100);
			} else {
				notify(response.message,'danger');
			}
		}
	});
}

/**
 * clickMenuHome
 * 
 * @param	{numberic}		site_menu_id
 */
function clickMenuHome (site_menu_id) {
	jQuery.ajax({
		type:'post',
		dataType:'json',
		url:site_url+'/admin/updateMenuHomeAjax/',
		data:{
			id:site_menu_id
		},
		success:function(response){
			if (response.status) {
				jQuery.cookie(prefix+'noti',response.message);
				jQuery.cookie(prefix+'noti_type','success');
				
				document.location.href = document.location.href;
			} else {
				notify(response.message,'danger');
			}
		}
	});
}

jQuery(function(){
	jQuery('#nestable').nestable({
		maxDepth:3,
		expandBtnHTML:'<button data-action="expand" type="button" class="btn btn-default"><i class="fa fa-plus fa-fw"></i></button>',
		collapseBtnHTML:'<button data-action="collapse" type="button" class="btn btn-default"><i class="fa fa-minus fa-fw"></i></button>'
	}).on('change',changeMenuIndex);
	
	changeMenuModel();
});
