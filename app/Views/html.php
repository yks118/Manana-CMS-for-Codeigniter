<?php echo doctype('html5'); ?>
<html lang="ko-KR">
	<head>
		<meta charset="UTF-8">
		<meta name="Generator" content="codeigniter <?php echo CodeIgniter\CodeIgniter::CI_VERSION; ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<link rel="canonical" href="<?php echo current_url(); ?>">
		<link rel="alternate" hreflang="x-default" href="<?php echo current_url(); ?>">

		<!-- Meta -->
		<?php
		foreach (\Config\Services::html()->getMeta() as $key => $row)
		{
			if ($key)
				echo "\t\t";
		?>
		<meta<?php foreach ($row as $key => $value) { echo ' ' . $key . '="' . $value . '"'; } ?>>
		<?php
			echo "\n";
		}
		?>

		<!-- site title -->
		<title><?php echo \Config\Services::html()->getTitleString(); ?></title>

		<!-- base -->
		<base href="<?php echo site_url(); ?>" target="_self">

		<!-- css -->
		<?php
		foreach (\Config\Services::html()->getCss() as $key => $css)
		{
			if ($key)
				echo "\t\t";
			echo '<link type="text/css" rel="stylesheet" href="' . $css . '">';
			echo "\n";
		}
		?>

		<!-- javascript -->
		<?php
		foreach (\Config\Services::html()->getJs('header') as $key => $js)
		{
			if ($key)
				echo "\t\t";
			echo '<script type="text/javascript" charset="UTF-8" src="' . $js . '"></script>';
			echo "\n";
		}
		?>
	</head>
	<body<?php foreach (\Config\Services::html()->getBodyAttribute() as $key => $value) { echo ' ' . $key . '="' . $value . '"'; } ?>>
		<?php /** @var string $layout */ echo $layout; ?>

		<!-- Javascript -->
		<?php
		foreach (\Config\Services::html()->getJs('footer') as $key => $js)
		{
			if ($key)
				echo "\t\t";
			echo '<script type="text/javascript" charset="UTF-8" src="' . $js . '"></script>';
			echo "\n";
		}
		?>
	</body>
</html>
