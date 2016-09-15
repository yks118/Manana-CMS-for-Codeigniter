<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="form-group">
	<button type="button" class="btn btn-default" onclick="clickFileUpload(this.form,'<?php echo $editor_id; ?>','<?php echo $model; ?>','<?php echo $model_id; ?>','<?php echo $file_action; ?>')"><?php echo lang('text_file_upload'); ?></button>
</div>

<ul class="list-inline files">
	<?php foreach ($files as $row) {
		if (empty($row['is_image'])) { ?>
	<li data-file-id="<?php echo $row['id']; ?>">
		<div class="btn-group">
			<button type="button" class="btn btn-default" onclick="clickInsertEditorHTML('<?php echo $editor_id; ?>',<?php echo $row['id']; ?>,'<?php echo $row['name']; ?>')">
				<?php echo $row['name']; ?>
			</button>
			<button type="button" class="btn btn-danger" onclick="clickFileDelete(this.form,<?php echo $row['id']; ?>)"><?php echo lang('text_delete'); ?></button>
		</div>
	</li>
		<?php }
	} ?>
</ul>

<ul class="list-unstyled files thumbnails row" data-li-class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
	<?php foreach ($files as $row) {
		if ($row['is_image']) { ?>
	<li class="col-lg-3 col-md-3 col-sm-4 col-xs-12" data-file-id="<?php echo $row['id']; ?>">
		<img src="<?php echo html_path($row['path']); ?>" alt="<?php echo $row['name']; ?>" />
		<div class="btn-group btn-group-justified">
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="clickInsertEditorHTML('<?php echo $editor_id; ?>',<?php echo $row['id']; ?>,'<?php echo $row['name']; ?>','<?php echo $row['path']; ?>')">
					<?php echo $row['name']; ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-danger" onclick="clickFileDelete(this.form,<?php echo $row['id']; ?>)"><?php echo lang('text_delete'); ?></button>
			</div>
		</div>
	</li>
		<?php }
	} ?>
</ul>