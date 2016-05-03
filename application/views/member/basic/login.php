<section id="loginMember" class="member">
	<div class="container">
		<?php
		$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
		echo form_open_multipart(base_url('/member/writeLogin/'),$attributes);
		?>
		
		<h2><?php echo lang('member_login'); ?></h2>
		
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon">
					<i class="fa fa-user fa-fw"></i>
				</div>
				<input type="text" class="form-control" name="member_username" id="member_username" required="required" />
			</div>
		</div>
		
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon">
					<i class="fa fa-key fa-fw"></i>
				</div>
				<input type="password" class="form-control" name="member_password" id="member_password" required="required" />
			</div>
		</div>
		
		<div class="text-right">
			<a target="_self" class="btn btn-default" href="<?php echo base_url('/member/join/'); ?>"><?php echo lang('member_join'); ?></a>
			<button type="submit" class="btn btn-primary"><?php echo lang('member_login'); ?></button>
		</div>
		
		<?php echo form_close(); ?>
	</div>
</section>