<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writeBasic" class="board">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/'.$this->model->now_menu['uri'].'/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="board_board_id" id="board_board_id" value="<?php echo (isset($data['board_id']))?$data['board_id']:''; ?>" />
	<input type="hidden" name="board_board_config_id" id="board_board_config_id" value="<?php echo (isset($data['board_config_id']))?$data['board_config_id']:''; ?>" />
	<input type="hidden" name="board_language" id="board_language" value="<?php echo $this->config->item('language'); ?>" />
	<input type="hidden" name="file_ids" id="file_ids" value="" />
	
	<div class="form-group">
		<label for="board_title"><?php echo lang('text_title'); ?></label>
		<input type="text" class="form-control" name="board_title" id="board_title" required="required" value="<?php echo (isset($data['title']))?$data['title']:''; ?>" />
	</div>
	
	<div class="form-group">
		<textarea name="board_document" id="board_document"><?php echo (isset($data['document']))?$data['document']:''; ?></textarea>
	</div>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'); ?>"><?php echo lang('text_list'); ?></a>
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('board_document'));
?>