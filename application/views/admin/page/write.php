<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writePageAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/page/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="page_id" id="page_id" value="<?php echo (isset($data['id']))?$data['id']:''; ?>" />
	<input type="hidden" name="page_page_id" id="page_page_id" value="<?php echo (isset($data['page_id']))?$data['page_id']:''; ?>" />
	<input type="hidden" name="page_language" id="page_language" value="<?php echo $this->config->item('language'); ?>" />
	<input type="hidden" name="file_ids" id="file_ids" value="" />
	
	<div class="form-group">
		<label for="page_title"><?php echo lang('text_title'); ?></label>
		<input type="text" class="form-control" id="page_title" name="page_title" maxlength="255" required="required" autofocus="autofocus" value="<?php echo (isset($data['title']))?$data['title']:''; ?>" />
	</div>
	
	<div class="form-group">
		<textarea name="page_document" id="page_document"><?php echo (isset($data['document']))?$data['document']:''; ?></textarea>
	</div>
	
	<?php
	$config = array();
	$config['model'] = 'page';
	$config['model_id'] = (isset($data['page_id']))?$data['page_id']:'0';
	$config['editor_id'] = 'page_document';
	$config['files'] = (isset($data['files']))?$data['files']:array();
	$config['file_action'] = 'file_upload';
	$this->load->view('file/'.$this->file->skin.'/view',$config);
	?>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/page/'); ?>"><?php echo lang('text_list'); ?></a>
		<button class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('page_document'));
?>