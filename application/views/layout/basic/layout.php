<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->model->css($path.'/css/basic.layout.less');
?>

<div id="basicLayout" class="layout">
	<div class="headerLayout">
		<div class="language dropdown pull-right">
			<button class="btn btn-link dropdown-toggle" type="button" data-toggle="dropdown">
				<?php echo ucwords($this->config->item('language')); ?>
				<span class="caret"></span>
			</button>
			
			<ul class="dropdown-menu">
				<li class="dropdown-header">Language</li>
				<?php foreach ($this->model->site['use_language'] as $language) { ?>
				<li>
					<a target="_self" href="javascript:clickLanguage('<?php echo $language; ?>');"><?php echo ucwords($language); ?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
		
		<a class="logo" target="_self" href="<?php echo base_url('/'); ?>"><?php echo $this->model->site['name']; ?></a>
		
		<ul class="lnb">
			<?php foreach ($this->model->menu as $lnb) { ?>
			<li class="lnb <?php echo implode(' ',$lnb['class']); ?>">
				<a class="lnb" target="<?php echo $lnb['target']; ?>" href="<?php echo $lnb['href']; ?>"><?php echo $lnb['name']; ?></a>
				
				<?php if (!empty($lnb['children'])) { ?>
				<ul class="snb">
					<?php foreach ($lnb['children'] as $snb) { ?>
					<li class="snb <?php echo implode(' ',$snb['class']); ?>">
						<a class="snb" target="<?php echo $snb['target']; ?>" href="<?php echo $snb['href']; ?>"><?php echo $snb['name']; ?></a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
	</div>
	
	<aside>
		<?php echo $loginForm; ?>
		<ul class="list-unstyled snb">
			<?php foreach ($this->model->menu as $lnb) {
				if (in_array('active',$lnb['class'])) {
					foreach ($lnb['children'] as $snb) { ?>
			<li class="snb <?php echo implode(' ',$snb['class']); ?>">
				<a class="snb" target="<?php echo $snb['target']; ?>" href="<?php echo $snb['href']; ?>"><?php echo $snb['name']; ?></a>
			</li>
					<?php }
				}
			} ?>
		</ul>
	</aside>
	
	<div class="documentLayout"><?php echo $page; ?></div>
</div>