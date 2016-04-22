<section id="siteInstall" class="install">
	<div class="container mt30">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Site Setting</h3>
			</div>
			<div class="panel-body">
				<?php
				$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
				echo form_open_multipart(base_url('/install/writeSite/'),$attributes);
				?>
				
				<div class="form-group">
					<label for="site_url">Site Url</label>
					<input type="text" class="form-control" id="site_url" name="site_url" value="<?php echo $_SERVER['HTTP_HOST']; ?>" maxlength="255" required="required" readonly="readonly" />
				</div>
				
				<div class="form-group">
					<label for="site_name"><?php echo lang('site_name'); ?></label>
					<input type="text" class="form-control" id="site_name" name="site_name" value="" autofocus="" maxlength="255" required="required" />
				</div>
				
				<div class="form-group">
					<label for="site_description"><?php echo lang('site_description'); ?></label>
					<input type="text" class="form-control" id="site_description" name="site_description" value="" maxlength="255" />
				</div>
				
				<div class="form-group">
					<label for="site_keywords"><?php echo lang('site_keywords'); ?></label>
					<input type="text" class="form-control" id="site_keywords" name="site_keywords" value="" maxlength="255" />
				</div>
				
				<div class="form-group">
					<label for="site_author"><?php echo lang('site_author'); ?></label>
					<input type="text" class="form-control" id="site_author" name="site_author" value="" maxlength="255" />
				</div>
				
				<div class="radio">
					<label class="radio-inline">
						<input type="radio" id="site_mobile_view_true" name="site_mobile_view" value="t" checked="checked" />
						<?php echo lang('site_mobile_view_true'); ?>
					</label>
					
					<label class="radio-inline">
						<input type="radio" id="site_mobile_view_false" name="site_mobile_view" value="f" />
						<?php echo lang('site_mobile_view_false'); ?>
					</label>
				</div>
				
				<div class="radio">
					<label class="radio-inline">
						<input type="radio" id="site_robots_true" name="site_robots" value="t" checked="checked" />
						<?php echo lang('site_robots_true'); ?>
					</label>
					
					<label class="radio-inline">
						<input type="radio" id="site_robots_false" name="site_robots" value="f" />
						<?php echo lang('site_robots_false'); ?>
					</label>
				</div>
				
				<div class="radio">
					<label class="radio-inline">
						<input type="radio" id="site_login_username" name="site_login" value="username" checked="checked" />
						<?php echo lang('site_login_username'); ?>
					</label>
					
					<label class="radio-inline">
						<input type="radio" id="site_login_email" name="site_login" value="email" />
						<?php echo lang('site_login_email'); ?>
					</label>
				</div>
				
				<div class="text-right">
					<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
				</div>
				
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>