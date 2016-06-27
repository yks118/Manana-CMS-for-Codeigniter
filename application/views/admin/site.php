<section id="siteAdmin" class="admin">
	<?php
	$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
	echo form_open_multipart(base_url('/admin/updateSiteForm/'),$attributes);
	?>
	
	<input type="hidden" name="site_id" id="site_id" value="<?php echo $data['id']; ?>" />
	
	<div class="form-group">
		<label for="site_url">Site Url</label>
		<input type="text" class="form-control" name="site_url" id="site_url" maxlength="255" required="required" value="<?php echo $data['url']; ?>" />
	</div>
	
	<div class="form-group">
		<label for="site_name"><?php echo lang('site_name'); ?></label>
		<input type="text" class="form-control" id="site_name" name="site_name" autofocus="" maxlength="255" required="required" value="<?php echo $data['name']; ?>" />
	</div>
	
	<div class="form-group">
		<label for="site_description"><?php echo lang('site_description'); ?></label>
		<input type="text" class="form-control" id="site_description" name="site_description" maxlength="255" value="<?php echo (isset($data['description']))?$data['description']:''; ?>" />
	</div>
	
	<div class="form-group">
		<label for="site_keywords"><?php echo lang('site_keywords'); ?></label>
		<input type="text" class="form-control" id="site_keywords" name="site_keywords" maxlength="255" value="<?php echo (isset($data['keywords']))?$data['keywords']:''; ?>" />
	</div>
	
	<div class="form-group">
		<label for="site_author"><?php echo lang('site_author'); ?></label>
		<input type="text" class="form-control" id="site_author" name="site_author" maxlength="255" value="<?php echo (isset($data['author']))?$data['author']:''; ?>" />
	</div>
	
	<div class="radio">
		<label class="radio-inline">
			<input type="radio" id="site_mobile_view_true" name="site_mobile_view" value="t" <?php echo ($data['mobile_view'] == 't')?'checked="checked"':''; ?> />
			<?php echo lang('site_mobile_view_true'); ?>
		</label>
		
		<label class="radio-inline">
			<input type="radio" id="site_mobile_view_false" name="site_mobile_view" value="f" <?php echo ($data['mobile_view'] == 'f')?'checked="checked"':''; ?> />
			<?php echo lang('site_mobile_view_false'); ?>
		</label>
	</div>
	
	<div class="radio">
		<label class="radio-inline">
			<input type="radio" id="site_robots_true" name="site_robots" value="t" <?php echo ($data['robots'] == 't')?'checked="checked"':''; ?> />
			<?php echo lang('site_robots_true'); ?>
		</label>
		
		<label class="radio-inline">
			<input type="radio" id="site_robots_false" name="site_robots" value="f" <?php echo ($data['robots'] == 'f')?'checked="checked"':''; ?> />
			<?php echo lang('site_robots_false'); ?>
		</label>
	</div>
	
	<div class="radio">
		<label class="radio-inline">
			<input type="radio" id="site_login_username" name="site_login" value="username" <?php echo ($data['login'] == 'username')?'checked="checked"':''; ?> />
			<?php echo lang('site_login_username'); ?>
		</label>
		
		<label class="radio-inline">
			<input type="radio" id="site_login_email" name="site_login" value="email" <?php echo ($data['login'] == 'email')?'checked="checked"':''; ?> />
			<?php echo lang('site_login_email'); ?>
		</label>
	</div>
	
	<div class="text-right">
		<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
	</div>
	
	<?php echo form_close(); ?>
</section>