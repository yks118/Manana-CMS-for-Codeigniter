<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writeConfigBoardAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/board/'.$action.'ConfigForm/'),$attributes);
	?>
	
	<input type="hidden" name="config_board_config_id" id="config_board_config_id" value="<?php echo (isset($data['board_config_id']))?$data['board_config_id']:0; ?>" />
	<input type="hidden" name="config_language" id="config_language" value="<?php echo $this->config->item('language'); ?>" />
	
	<div class="form-group">
		<label for="config_name"><?php echo lang('text_name'); ?></label>
		<input type="text" class="form-control" id="config_name" name="config_name" maxlength="255" required="required" autofocus="autofocus" value="<?php echo (isset($data['name']))?$data['name']:''; ?>" />
	</div>
	
	<div class="form-group">
		<label for="config_skin"><?php echo lang('text_skin'); ?></label>
		<select class="form-control" id="config_skin" name="config_skin">
			<?php foreach ($skin_list as $skin) { ?>
			<option value="<?php echo $skin; ?>" <?php echo (isset($data['skin']) && $data['skin'] == $skin)?'selected="selected"':''; ?>><?php echo ucfirst($skin); ?></option>
			<?php } ?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="config_limit"><?php echo lang('text_per_page'); ?></label>
		<input type="text" class="form-control" id="config_limit" name="config_limit" maxlength="255" required="required" value="<?php echo (isset($data['limit']))?$data['limit']:'20'; ?>" />
	</div>
	
	<div class="form-group">
		<label for="config_order_by"><?php echo lang('text_order_by'); ?></label>
		<select class="form-control" id="config_order_by" name="config_order_by">
			<option value="id|desc" <?php echo ((isset($data['order_by']) && $data['order_by'] == 'id') && (isset($data['order_by_sort']) && $data['order_by_sort'] == 'desc'))?'selected="selected"':''; ?>><?php echo lang('board_order_by_id_desc'); ?></option>
			<option value="id|asc" <?php echo ((isset($data['order_by']) && $data['order_by'] == 'id') && (isset($data['order_by_sort']) && $data['order_by_sort'] == 'asc'))?'selected="selected"':''; ?>><?php echo lang('board_order_by_id_asc'); ?></option>
			<option value="last_datetime|desc" <?php echo ((isset($data['order_by']) && $data['order_by'] == 'last_datetime') && (isset($data['order_by_sort']) && $data['order_by_sort'] == 'desc'))?'selected="selected"':''; ?>><?php echo lang('board_order_by_last_desc'); ?></option>
			<option value="last_datetime|asc" <?php echo ((isset($data['order_by']) && $data['order_by'] == 'last_datetime') && (isset($data['order_by_sort']) && $data['order_by_sort'] == 'asc'))?'selected="selected"':''; ?>><?php echo lang('board_order_by_last_asc'); ?></option>
		</select>
	</div>
	
	<div class="form-group">
		<label for="config_use_secret" class="mb0"><?php echo lang('text_use_secret'); ?></label>
		<div>
			<label class="radio-inline">
				<input type="radio" id="config_use_secret_true" name="config_use_secret" value="t" <?php echo (!isset($data['use_secret']) || $data['use_secret'] == 't')?'checked="checked"':''; ?> />
				<?php echo lang('text_use'); ?>
			</label>
			<label class="radio-inline">
				<input type="radio" id="config_use_secret_false" name="config_use_secret" value="f" <?php echo (isset($data['use_secret']) && $data['use_secret'] == 'f')?'checked="checked"':''; ?> />
				<?php echo lang('text_not_use'); ?>
			</label>
		</div>
	</div>
	
	<div class="form-group">
		<label for="config_default_secret" class="mb0"><?php echo lang('text_default_secret'); ?></label>
		<div>
			<label class="radio-inline">
				<input type="radio" id="config_default_secret_true" name="config_default_secret" value="t" <?php echo (isset($data['default_secret']) && $data['default_secret'] == 't')?'checked="checked"':''; ?> />
				<?php echo lang('text_use'); ?>
			</label>
			<label class="radio-inline">
				<input type="radio" id="config_default_secret_false" name="config_default_secret" value="f" <?php echo (!isset($data['default_secret']) || $data['default_secret'] == 'f')?'checked="checked"':''; ?> />
				<?php echo lang('text_not_use'); ?>
			</label>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_list" class="mb0"><?php echo lang('text_model_auth_list'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_list_<?php echo $row['id']; ?>" name="model_auth_list[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['list'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_write" class="mb0"><?php echo lang('text_model_auth_write'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_write_<?php echo $row['id']; ?>" name="model_auth_write[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['write'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_view" class="mb0"><?php echo lang('text_model_auth_view'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_view_<?php echo $row['id']; ?>" name="model_auth_view[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['view'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_reply" class="mb0"><?php echo lang('text_model_auth_reply'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_reply_<?php echo $row['id']; ?>" name="model_auth_reply[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['reply'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="config_comment_use_secret" class="mb0"><?php echo lang('text_comment_use_secret'); ?></label>
		<div>
			<label class="radio-inline">
				<input type="radio" id="config_comment_use_secret_true" name="config_comment_use_secret" value="t" <?php echo (!isset($data['comment_use_secret']) || $data['comment_use_secret'] == 't')?'checked="checked"':''; ?> />
				<?php echo lang('text_use'); ?>
			</label>
			<label class="radio-inline">
				<input type="radio" id="config_comment_use_secret_false" name="config_comment_use_secret" value="f" <?php echo (isset($data['comment_use_secret']) && $data['comment_use_secret'] == 'f')?'checked="checked"':''; ?> />
				<?php echo lang('text_not_use'); ?>
			</label>
		</div>
	</div>
	
	<div class="form-group">
		<label for="config_comment_default_secret" class="mb0"><?php echo lang('text_comment_default_secret'); ?></label>
		<div>
			<label class="radio-inline">
				<input type="radio" id="config_comment_default_secret_true" name="config_comment_default_secret" value="t" <?php echo (isset($data['comment_default_secret']) && $data['comment_default_secret'] == 't')?'checked="checked"':''; ?> />
				<?php echo lang('text_use'); ?>
			</label>
			<label class="radio-inline">
				<input type="radio" id="config_comment_default_secret_false" name="config_comment_default_secret" value="f" <?php echo (!isset($data['comment_default_secret']) || $data['comment_default_secret'] == 'f')?'checked="checked"':''; ?> />
				<?php echo lang('text_not_use'); ?>
			</label>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_comment_list" class="mb0"><?php echo lang('text_model_auth_comment_list'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_comment_list_<?php echo $row['id']; ?>" name="model_auth_comment_list[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['comment_list'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_comment_write" class="mb0"><?php echo lang('text_model_auth_comment_write'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_comment_write_<?php echo $row['id']; ?>" name="model_auth_comment_write[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['comment_write'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="model_auth_comment_reply" class="mb0"><?php echo lang('text_model_auth_comment_reply'); ?></label>
		<div>
			<?php foreach ($member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="model_auth_comment_reply_<?php echo $row['id']; ?>" name="model_auth_comment_reply[]" value="<?php echo $row['id']; ?>"
					<?php echo (($action == 'write' && $row['id'] == $member_grade_list[0]['id']) || ($action == 'update' && in_array($row['id'],$auth['comment_reply'])))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/board/'); ?>"><?php echo lang('text_list'); ?></a>
		<button class="btn btn-primary" type="submit"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>