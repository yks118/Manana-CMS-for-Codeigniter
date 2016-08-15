<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="writePageAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/page/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="page_id" id="page_id" value="<?php echo (isset($data['id']))?$data['id']:''; ?>" />
	<input type="hidden" name="page_page_id" id="page_page_id" value="<?php echo (isset($data['page_id']))?$data['page_id']:''; ?>" />
	<input type="hidden" name="page_language" id="page_language" value="<?php echo $this->config->item('language'); ?>" />
	<input type="hidden" name="file_ids" id="file_ids" value="" />
	
	<div class="form-group">
		<label for="page_title"><?php echo lang('text_title'); ?></label>
		<input type="text" class="form-control" id="page_title" name="page_title" maxlength="255" required="required" autofocus="autofocus" value="<?php echo (isset($data['title']))?$data['title']:''; ?>" />
	</div>
	
	<div class="form-group">
		<textarea name="page_document" id="page_document"><?php echo (isset($data['document']))?$data['document']:''; ?></textarea>
	</div>
	
	<div class="form-group">
		<button type="button" class="btn btn-default" onclick="clickFileUpload(this.form,'page')"><?php echo lang('text_file_upload'); ?></button>
	</div>
	
	<ul class="list-inline files">
		<?php if (isset($data['files'])) {
			foreach ($data['files'] as $row) {
				if (empty($row['is_image'])) { ?>
		<li data-file-id="<?php echo $row['id']; ?>">
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="clickInsertEditorHTML('page_document',<?php echo $row['id']; ?>,'<?php echo $row['name']; ?>')">
					<?php echo $row['name']; ?>
				</button>
				<button type="button" class="btn btn-danger" onclick="clickFileDelete(this.form,<?php echo $row['id']; ?>)"><?php echo lang('text_delete'); ?></button>
			</div>
		</li>
				<?php }
			}
		} ?>
	</ul>
	
	<ul class="list-unstyled files thumbnails row" data-li-class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
		<?php if (isset($data['files'])) {
			foreach ($data['files'] as $row) {
				if ($row['is_image']) { ?>
		<li class="col-lg-3 col-md-3 col-sm-4 col-xs-12" data-file-id="<?php echo $row['id']; ?>">
			<img src="<?php echo html_path($row['path']); ?>" alt="<?php echo $row['name']; ?>" />
			<div class="btn-group btn-group-justified">
				<div class="btn-group">
					<button type="button" class="btn btn-default" onclick="clickInsertEditorHTML('page_document',<?php echo $row['id']; ?>,'<?php echo $row['name']; ?>','<?php echo $row['path']; ?>')">
						<?php echo $row['name']; ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-danger" onclick="clickFileDelete(this.form,<?php echo $row['id']; ?>)"><?php echo lang('text_delete'); ?></button>
				</div>
			</div>
		</li>
				<?php }
			}
		} ?>
	</ul>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/admin/page/'); ?>"><?php echo lang('text_list'); ?></a>
		<button class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('page_document'));
?>