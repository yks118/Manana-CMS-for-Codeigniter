/**
 * clickConfigDelete
 * 
 * @param	{numberic}	id
 */
function clickConfigDelete (id) {
	if (confirm(read_language('system_delete_question'))) {
		jQuery.ajax({
			type:'post',
			dataType:'json',
			url:site_url+'/admin/board/deleteConfigAjax/',
			data:{
				id:id
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
}
