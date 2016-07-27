<section id="loginInfoMember" class="member">
	<ul class="list-unstyled">
		<li><?php echo lang('member_username'); ?></li>
		<li class="text-right"><?php echo $this->member->data['username']; ?></li>
		<li><?php echo lang('member_name'); ?></li>
		<li class="text-right"><?php echo $this->member->data['name']; ?></li>
		<li><?php echo lang('member_grade'); ?></li>
		<li class="text-right"><?php echo implode(', ',$this->member->data['grade']); ?></li>
	</ul>
	<div class="text-right">
		<?php if ($this->member->check_admin()) { ?>
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/'); ?>">Admin</a>
		<?php } ?>
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/member/update/'); ?>"><?php echo lang('text_update'); ?></a>
		<a class="btn btn-danger" target="hIframe" href="<?php echo base_url('/member/logout/'); ?>"><?php echo lang('member_logout'); ?></a>
	</div>
</section>