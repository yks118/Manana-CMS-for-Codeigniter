<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="passwordBasic" class="board">
	<h2 class="text-center"><?php echo $data['title']; ?></h2>
	
	<div class="panel panel-default">
		<div class="panel-body">
			<p><?php echo lang('board_password_text'); ?></p>
			
			<?php
			$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
			echo form_open_multipart(base_url('/'.$this->model->now_menu['uri'].'/checkPasswordForm/'),$attributes);
			?>
			
			<input type="hidden" name="board_board_id" id="board_board_id" value="<?php echo $data['board_id']; ?>" />
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
			
			<div class="form-group">
				<label for="board_password"><?php echo lang('member_password'); ?></label>
				<input type="password" class="form-control" name="board_password" id="board_password" required="required" value="" />
			</div>
			
			<div class="text-right">
				<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'); ?>"><?php echo lang('text_list'); ?></a>
				<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
			</div>
			
			<?php echo form_close(); ?>
		</div>
	</div>
</section>