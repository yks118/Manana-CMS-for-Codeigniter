<div class="card">
	<div class="card-header">
		<h4 class="card-title">Account Setting</h4>
	</div>
	<div class="card-body">
		<?php echo form_open(); ?>

		<div class="form-group">
			<label for="name">Name</label>
			<input
				type="text" class="form-control" name="name" id="name" required
				value="<?php echo set_value('name', account()->name); ?>"
			>
		</div>

		<div class="text-right">
			<button type="submit" class="btn btn-fill btn-primary">Submit</button>
		</div>

		<?php echo form_close(); ?>
	</div>
</div>