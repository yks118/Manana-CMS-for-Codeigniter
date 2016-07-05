<section id="writePageAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/page/'.$action.'Form/'),$attributes);
	?>
	
	<input type="hidden" name="page_id" id="page_id" value="<?php echo (isset($data['id']))?$data['id']:''; ?>" />
	<input type="hidden" name="file_ids" id="file_ids" value="" />
	
	<div class="form-group">
		<label for="page_title"><?php echo lang('text_title'); ?></label>
		<input type="text" class="form-control" id="page_title" name="page_title" maxlength="255" required="required" autofocus="autofocus" value="<?php echo (isset($data['name']))?$data['name']:''; ?>" />
	</div>
	
	<div class="form-group">
		<textarea name="page_document" id="page_document"></textarea>
	</div>
	
	<div class="form-group">
		<button type="button" class="btn btn-default" onclick="clickFileUpload(this.form,'page')">File Upload</button>
	</div>
	
	<ul class="list-unstyled files">
		<li>
			<div class="btn-group">
				<button class="btn btn-default">file name</button>
				<button class="btn btn-danger">Delete</button>
			</div>
		</li>
	</ul>
	
	<ul class="list-unstyled files thumbnails row" data-li-class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
		<?php for ($i = 0; $i <= 4; $i++) { ?>
		<li class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<img src="/upload/koikake068.png" />
			<div class="btn-group btn-group-justified">
				<div class="btn-group">
					<button class="btn btn-default">Insert</button>
				</div>
				<div class="btn-group">
					<button class="btn btn-danger">Delete</button>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
	
	<div class="text-right">
		<button class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>

<?php
// set editor
echo js($this->editor->write_js('page_document'));
?>