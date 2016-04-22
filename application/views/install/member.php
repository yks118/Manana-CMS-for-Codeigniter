<section id="memberInstall" class="install">
	<div class="container mt30">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Member Setting</h3>
			</div>
			<div class="panel-body">
				<?php
				$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
				echo form_open_multipart(base_url('/install/writeMember/'),$attributes);
				?>
				
				<div class="form-group">
					<label for="member_username"><?php echo lang('member_username'); ?></label>
					<input type="text" class="form-control" id="member_username" name="member_username" required="required" minlength="6" maxlength="255" value="" />
				</div>
				
				<div class="form-group">
					<label for="member_password"><?php echo lang('member_password'); ?></label>
					<input type="password" class="form-control" id="member_password" name="member_password" required="required" minlength="6" maxlength="255" value="" />
				</div>
				
				<div class="form-group">
					<label for="member_name"><?php echo lang('member_name'); ?></label>
					<input type="text" class="form-control" id="member_name" name="member_name" required="required" minlength="6" maxlength="255" value="" />
				</div>
				
				<div class="form-group">
					<label for="member_email"><?php echo lang('member_email'); ?></label>
					<input type="text" class="form-control" id="member_email" name="member_email" required="required" maxlength="255" value="" />
				</div>
				
				<div class="text-right">
					<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
				</div>
				
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>