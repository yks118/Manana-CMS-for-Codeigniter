<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->model->css($path.'/css/basic.member.less');
?>

<section id="writeMember" class="member">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/member/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="member_id" id="member_id" value="<?php echo (isset($data['id']))?$data['id']:''; ?>" />
	<input type="hidden" name="information_language" id="information_language" value="<?php echo $this->config->item('language'); ?>" />
	
	<div class="form-group">
		<label for="member_username"><?php echo lang('member_username'); ?></label>
		<input type="text" class="form-control" name="member_username" id="member_username" required="required" maxlength="255" value="<?php echo (isset($data['username']))?$data['username']:''; ?>"
			<?php echo ($action == 'update')?'readonly="readonly"':''; ?>
		/>
	</div>
	
	<div class="form-group">
		<label for="member_password"><?php echo lang('member_password'); ?></label>
		<input type="password" class="form-control" name="member_password" id="member_password" maxlength="255" value="" />
	</div>
	
	<div class="form-group">
		<label for="member_name"><?php echo lang('member_name'); ?></label>
		<input type="text" class="form-control" name="member_name" id="member_name" required="required" maxlength="255" value="<?php echo (isset($data['name']))?$data['name']:''; ?>" />
	</div>
	
	<div class="form-group">
		<label for="member_email"><?php echo lang('member_email'); ?></label>
		<input type="text" class="form-control" name="member_email" id="member_email" required="required" maxlength="255" value="<?php echo (isset($data['email']))?$data['email']:''; ?>" />
	</div>
	
	<div class="form-group">
		<label for="information_description"><?php echo lang('member_description'); ?></label>
		<textarea class="form-control" name="information_description" id="information_description"><?php echo (isset($data['description']))?$data['description']:''; ?></textarea>
	</div>
	
	<div class="form-group">
		<label for="profile_photo">
			<?php echo lang('member_profile_photo'); ?>
			<small>(120x120)</small>
		</label>
		<?php if (isset($data['profile_photo'][0])) { ?>
		<img class="profile_photo" src="<?php echo html_path($data['profile_photo'][0]['path']); ?>" alt="<?php echo $data['name']; ?>" />
		<label>
			<input type="checkbox" name="delete_profile_photo" id="delete_profile_photo" value="<?php echo $data['profile_photo'][0]['id']; ?>" />
			<?php echo lang('text_delete'); ?>
		</label>
		<?php } else { ?>
		<input type="file" name="file" id="profile_photo" />
		<?php } ?>
	</div>
	
	<div class="form-group">
		<label for="now_password"><?php echo lang('member_now_password'); ?></label>
		<input type="password" class="form-control" name="now_password" id="now_password" required="required" value="" />
	</div>
	
	<div class="text-right">
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('information_description'));
?>