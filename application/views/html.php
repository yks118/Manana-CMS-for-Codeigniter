<?php echo doctype('html5'); ?>
<html lang="<?php echo $site_lang; ?>">
	<head>
		<!-- META -->
		<meta charset="UTF-8">
		
		<meta name="Generator" content="codeigniter">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<!-- SEO -->
		<link rel="canonical" href="<?php echo current_url(); ?>" />
		<link rel="alternate" hreflang="x-default" href="<?php echo current_url(); ?>" />
		
		<?php if ($this->model->site['robots'] == 't') { ?>
		<meta name="robots" content="all" />
		<?php } else { ?>
		<meta name="robots" content="noindex,nofollow" />
		<?php } ?>
		
		<?php if ($this->model->site['mobile_view'] == 't') { ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
		<?php } ?>
		
		<!-- site title -->
		<title><?php echo $site_title; ?></title>
		
		<!-- css -->
		<?php foreach ($this->model->css as $row) { ?>
		<link type="text/css" rel="<?php echo $row['type']; ?>" href="<?php echo $row['path']; ?>" />
		<?php } ?>
		
		<!-- javascript -->
		<?php foreach ($this->model->js['header'] as $path) { ?>
		<script type="text/javascript" charset="UTF-8" src="<?php echo $path; ?>"></script>
		<?php } ?>
		
		<!-- Default Setting -->
		<script type="text/javascript" charset="UTF-8">
		var form;
		var prefix = "<?php echo $this->config->item('cookie_prefix'); ?>";
		var site_url = "<?php echo (isset($this->model->site['url']))?'//'.$this->model->site['url']:base_url('/'); ?>";
		var csrf = {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
		</script>
	</head>
	<body>
		<?php echo $layout; ?>
		
		<!-- File Upload Form -->
		<?php
		$attributes = array('name'=>'ffileupload','id'=>'ffileupload','target'=>'hIframe');
		echo form_open_multipart(base_url('/file/upload/'),$attributes);
		?>
		
		<input type="hidden" name="model" value="" />
		<input type="hidden" name="model_id" value="" />
		<input type="hidden" name="action" value="" />
		<input class="hidden" type="file" name="file" />
		
		<?php echo form_close(); ?>
		
		<!-- HIDDEN FRAME -->
		<iframe id="hIframe" name="hIframe" src="<?php echo base_url('/manana/notify/'); ?>"></iframe>
		
		<!-- javascript -->
		<?php foreach ($this->model->js['footer'] as $path) { ?>
		<script type="text/javascript" charset="UTF-8" src="<?php echo $path; ?>"></script>
		<?php } ?>
		
		<!-- Placeholder for IE9 -->
		<!--[if IE 9 ]>
			<script src="vendors/bower_components/jquery-placeholder/jquery.placeholder.min.js"></script>
		<![endif]-->
	</body>
</html>