<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writeBasic" class="board">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/'.$this->model->now_menu['uri'].'/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="board_board_id" id="board_board_id" value="<?php echo (isset($data['board_id']))?$data['board_id']:0; ?>" />
	<input type="hidden" name="board_board_config_id" id="board_board_config_id" value="<?php echo (isset($data['board_config_id']))?$data['board_config_id']:0; ?>" />
	<input type="hidden" name="board_parent_id" id="board_parent_id" value="<?php echo (isset($data['parent_id']))?$data['parent_id']:0; ?>" />
	<input type="hidden" name="board_language" id="board_language" value="<?php echo $this->config->item('language'); ?>" />
	<input type="hidden" name="file_ids" id="file_ids" value="" />
	
	<?php if ($this->board->is_admin || ($this->board->configure['use_secret'] == 't' && $this->board->configure['default_secret'] == 'f')) { ?>
	<div class="form-group">
		<?php if ($this->board->is_admin) { ?>
		<label class="checkbox-inline">
			<input type="checkbox" name="board_is_notice" id="board_is_notice" value="t" />
			<?php echo lang('text_notice'); ?>
		</label>
		<?php } ?>
		
		<?php if ($this->board->configure['use_secret'] == 't' && $this->board->configure['default_secret'] == 'f') { ?>
		<label class="checkbox-inline">
			<input type="checkbox" name="board_is_secret" id="board_is_secret" value="t" />
			<?php echo lang('text_secret'); ?>
		</label>
		<?php } ?>
	</div>
	<?php } ?>
	
	<div class="form-group">
		<label for="board_title"><?php echo lang('text_title'); ?></label>
		<input type="text" class="form-control" name="board_title" id="board_title" required="required" value="<?php echo (isset($data['title']))?$data['title']:''; ?>" />
	</div>
	
	<div class="form-group">
		<textarea name="board_document" id="board_document"><?php echo (isset($data['document']))?$data['document']:''; ?></textarea>
	</div>
	
	<?php
	$config = array();
	$config['model'] = 'board';
	$config['model_id'] = (isset($data['board_id']))?$data['board_id']:'0';
	$config['editor_id'] = 'board_document';
	$config['files'] = (isset($data['files']))?$data['files']:array();
	$config['file_action'] = 'file_upload';
	$this->load->view('file/'.$this->file->skin.'/view',$config);
	?>
	
	<div class="text-right">
		<?php if ($action == 'update') { ?>
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->uri->segment(1).'/'.$data['board_id'].'/'); ?>"><?php echo lang('text_cancel'); ?></a>
		<?php } else { ?>
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'); ?>"><?php echo lang('text_list'); ?></a>
		<?php } ?>
		
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('board_document'));
?>