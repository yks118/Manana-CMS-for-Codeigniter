<?php echo doctype('html5'); ?>
<html lang="<?php echo $site_lang; ?>">
	<head>
		<!-- META -->
		<meta charset="UTF-8">
		
		<meta name="Generator" content="codeigniter">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
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
		var prefix = "<?php echo $this->config->item('cookie_prefix'); ?>";
		var site_url = "<?php echo (isset($this->model->site['url']))?$this->model->site['url']:base_url('/'); ?>";
		</script>
	</head>
	<body>
		<?php echo $layout; ?>
		
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