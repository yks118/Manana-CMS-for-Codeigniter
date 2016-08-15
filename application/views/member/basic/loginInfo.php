<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->model->css($path.'/css/basic.member.less');
?>

<section id="loginInfoMember" class="member">
	<div class="clearfix">
		<div class="pull-left">
			<?php if (isset($this->member->data['profile_photo'][0])) { ?>
			<img class="profile_photo" src="<?php echo html_path($this->member->data['profile_photo'][0]['path']); ?>" alt="<?php echo $this->member->data['name']; ?>" />
			<?php } else { ?>
			<img class="profile_photo" src="//placehold.it/120x120" alt="<?php echo $this->member->data['name']; ?>" />
			<?php } ?>
		</div>
		<div class="pull-right profile">
			<ul class="list-unstyled">
				<li><?php echo lang('member_username'); ?></li>
				<li class="text-right">
					<a target="_self" href="<?php echo base_url('/member/information/'.$this->member->data['username'].'/'); ?>">
						<?php echo $this->member->data['username']; ?>
					</a>
				</li>
				<li><?php echo lang('member_name'); ?></li>
				<li class="text-right"><?php echo $this->member->data['name']; ?></li>
				<li><?php echo lang('member_grade'); ?></li>
				<li class="text-right"><?php echo implode(', ',$this->member->data['grade']); ?></li>
			</ul>
		</div>
	</div>
	<div class="text-right">
		<?php if ($this->member->check_admin()) { ?>
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/'); ?>">Admin</a>
		<?php } ?>
		<a class="btn btn-danger" target="hIframe" href="<?php echo base_url('/member/logout/'); ?>"><?php echo lang('member_logout'); ?></a>
	</div>
</section>