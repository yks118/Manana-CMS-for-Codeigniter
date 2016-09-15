<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="informationMember" class="member">
	<div class="text-center mb20">
		<?php if (isset($data['profile_photo'][0])) { ?>
		<img class="profile_photo" src="<?php echo html_path($data['profile_photo'][0]['path']); ?>" alt="<?php echo $data['name']; ?>" />
		<?php } else { ?>
		<img class="profile_photo" src="holder.js/120x120" alt="<?php echo $data['name']; ?>" />
		<?php } ?>
	</div>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_username'); ?></dt>
		<dd><?php echo $data['username']; ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_name'); ?></dt>
		<dd><?php echo $data['name']; ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_grade'); ?></dt>
		<dd><?php echo implode(', ',$data['grade']);; ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_description'); ?></dt>
		<dd><?php echo $data['description']; ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_join_date'); ?></dt>
		<dd><?php echo datetime($data['write_datetime']); ?></dd>
	</dl>
	
	<dl class="dl-horizontal">
		<dt><?php echo lang('member_last_login'); ?></dt>
		<dd><?php echo datetime($data['last_login']); ?></dd>
	</dl>
	
	<div class="text-right">
		<?php if (isset($this->member->data['username']) && $username == $this->member->data['username']) { ?>
		<a class="btn btn-primary" href="<?php echo base_url('/member/update/'); ?>"><?php echo lang('text_update'); ?></a>
		<?php } ?>
	</div>
</section>