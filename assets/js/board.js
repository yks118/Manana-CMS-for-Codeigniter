/**
 * clickDelete
 * 
 * board document delete
 * 
 * @param	{numberic}		id		board.board_id
 */
function clickDelete (id) {
	if (confirm(read_language('system_delete_question'))) {
		jQuery.ajax({
			type:'post',
			dataType:'json',
			url:site_url+'/'+segment[1]+'/deleteAjax/'+id+'/',
			data:{
				
			},
			success:function(response){
				if (response.status) {
					jQuery.cookie(prefix+'noti',response.message);
					jQuery.cookie(prefix+'noti_type','success');
					
					document.location.href = site_url+'/'+segment[1]+'/';
				} else {
					notify(response.message,'danger');
				}
			}
		});
	}
}
