<div class="card">
	<div class="card-header">
		<h4 class="card-title">Sign In</h4>
	</div>
	<div class="card-body">
		<?php echo form_open(); ?>

		<div class="form-group">
			<label for="username">Username</label>
			<input
				type="text" class="form-control" name="username" id="username" required
				value="<?php echo set_value('username', ''); ?>"
			>
		</div>

		<div class="form-group">
			<label for="password">Password</label>
			<input
				type="password" class="form-control" name="password" id="password" required
				value=""
			>
		</div>

		<div class="text-right">
			<button type="submit" class="btn btn-fill btn-primary">Submit</button>
		</div>

		<?php echo form_close(); ?>
	</div>
</div>