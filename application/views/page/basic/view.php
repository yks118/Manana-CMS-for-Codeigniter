<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ($this->page->auth['update']) {
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/page/updateForm/'),$attributes); ?>
	
	<input type="hidden" name="page_id" id="page_id" value="<?php echo (isset($data['id']))?$data['id']:0; ?>" />
	<input type="hidden" name="page_page_id" id="page_pageid" value="<?php echo (isset($data['page_id']))?$data['page_id']:0; ?>" />
	<input type="hidden" name="page_language" id="page_language" value="<?php echo $this->config->item('language'); ?>" />
	
	<textarea id="page_document" name="page_document"><?php echo $data['document']; ?></textarea>
	
	<div class="text-right">
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close();
	
	// set editor
	echo js($this->editor->write_js('page_document',TRUE));
} else { ?>
<div id="page_document"><?php echo $data['document']; ?></div>
<?php } ?>