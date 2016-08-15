<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writeMemberAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/member/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="member_id" id="member_id" value="<?php echo (isset($data['id']))?$data['id']:''; ?>" />
	<input type="hidden" name="language" id="language" value="<?php echo $this->config->item('language'); ?>" />
	
	<div class="form-group">
		<label for="member_username"><?php echo lang('member_username'); ?></label>
		<input type="text" class="form-control" id="member_username" name="member_username" maxlength="255"
			<?php echo ($action == 'update')?'readonly="readonly"':''; ?>
			value="<?php echo (isset($data['username']))?$data['username']:''; ?>"
		/>
	</div>
	
	<div class="form-group">
		<label for="member_password"><?php echo lang('member_password'); ?></label>
		<input type="password" class="form-control" id="member_password" name="member_password" maxlength="255" value="" />
	</div>
	
	<div class="form-group">
		<label for="member_name"><?php echo lang('member_name'); ?></label>
		<input type="text" class="form-control" id="member_name" name="member_name" maxlength="255" required="required"
			value="<?php echo (isset($data['name']))?$data['name']:''; ?>"
		/>
	</div>
	
	<div class="form-group">
		<label for="member_email"><?php echo lang('member_email'); ?></label>
		<input type="text" class="form-control" id="member_email" name="member_email" maxlength="255" required="required"
			value="<?php echo (isset($data['email']))?$data['email']:''; ?>"
		/>
	</div>
	
	<div class="form-group">
		<label for="member_description"><?php echo lang('member_memo'); ?></label>
		<textarea class="form-control" id="member_description" name="member_description"><?php echo (isset($data['memo']))?$data['memo']:''; ?></textarea>
	</div>
	
	<div class="form-group">
		<label for="member_information_description"><?php echo lang('member_description'); ?></label>
		<textarea class="form-control" id="member_information_description" name="member_information_description"><?php echo (isset($data['description']))?$data['description']:''; ?></textarea>
	</div>
	
	<div class="form-group">
		<label for="grade" class="mb0"><?php echo lang('member_grade'); ?></label>
		<div>
			<?php foreach ($site_member_grade_list as $row) { ?>
			<label class="checkbox-inline">
				<input type="checkbox" id="grade_<?php echo $row['id']; ?>" name="grade[]" value="<?php echo $row['id']; ?>"
					<?php echo (isset($this->member->data['grade'][$row['id']]))?'checked="checked"':''; ?>
				/>
				<?php echo $row['name']; ?>
			</label>
			<?php } ?>
		</div>
	</div>
	
	<?php if ($action == 'update') { ?>
	<div class="form-group">
		<label for="write_datetime"><?php echo lang('member_join_date'); ?></label>
		<input type="text" class="form-control" readonly="readonly" value="<?php echo $data['write_datetime']; ?>" />
	</div>
	
	<div class="form-group">
		<label for="last_login"><?php echo lang('member_last_login'); ?></label>
		<input type="text" class="form-control" readonly="readonly" value="<?php echo $data['last_login']; ?>" />
	</div>
	<?php } ?>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/member/'); ?>"><?php echo lang('text_list'); ?></a>
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>