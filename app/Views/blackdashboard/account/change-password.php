<div class="card">
	<div class="card-header">
		<h4 class="card-title">Change Password</h4>
	</div>
	<div class="card-body">
		<?php echo form_open(); ?>

		<div class="form-group">
			<label for="new_password">New Password</label>
			<input
				type="password" class="form-control" name="new_password" id="new_password" required
				value=""
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