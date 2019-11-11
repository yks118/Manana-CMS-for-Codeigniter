<section id="samples-form">
	<?php echo form_open(); ?>

	<div>
		<label for="username">Username</label>
		<input
			type="text" name="username" id="username" autocomplete="off"
			value="<?php echo set_value('username', ''); ?>"
		>
	</div>

	<div>
		<label for="password">Password</label>
		<input type="password" name="password" id="password" autocomplete="off" value="">
	</div>

	<?php echo form_close(); ?>
</section>