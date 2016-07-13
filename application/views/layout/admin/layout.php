<?php
// set css
$this->model->css($path.'/css/admin.layout.less');

// set js
$this->model->js($path.'/js/admin.layout.js');
?>

<div id="adminLayout" class="layout">
	<div id="headerLayout">
		<button class="btn btn-link pull-left" type="button" onclick="clickToggleLnb()">
			<i class="fa fa-bars fa-fw"></i>
		</button>
		<ul class="list-inline pull-right">
			<li>
				<a class="fa fa-fw fa-envelope" target="_self" href="javascript:void(0);">
					<span>1</span>
				</a>
			</li>
			<li>
				<a class="fa fa-fw fa-bell" target="_self" href="javascript:void(0);">
					<span>1</span>
				</a>
			</li>
			<li class="dropdown">
				<a class="dropdown-toggle text-capitalize" data-toggle="dropdown" role="button" href="#"><?php echo language($this->config->item('language')); ?></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php foreach ($this->model->site['use_language'] as $language) { ?>
					<li>
						<a class="text-capitalize" target="_self" href="javascript:clickLanguage('<?php echo $language; ?>');"><?php echo $language; ?></a>
					</li>
					<?php } ?>
				</ul>
			</li>
		</ul>
	</div>
	<aside id="lnbLayout">
		<ul class="list-unstyled">
			<li class="siteTitle">
				<a target="_self" href="<?php echo base_url('/'); ?>"><?php echo $this->model->site['name']; ?></a>
			</li>
			<?php foreach ($this->model->menu as $lnb_key => $lnb_data) { ?>
			<li class="lnb <?php echo ($this->uri->segment(2) == $lnb_key)?'active':''; ?>">
				<a class="lnb" target="<?php echo $lnb_data['target']; ?>" href="<?php echo $lnb_data['href']; ?>"><?php echo $lnb_data['name']; ?></a>
				
				<?php if (isset($lnb_data['children'])) { ?>
				<ul class="list-unstyled">
					<?php foreach ($lnb_data['children'] as $snb_key => $snb_data) { ?>
					<li class="snb <?php echo ($this->uri->segment(3) == $snb_key)?'active':''; ?>">
						<a class="snb" target="<?php echo $snb_data['target']; ?>" href="<?php echo $snb_data['href']; ?>"><?php echo $snb_data['name']; ?></a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
	</aside>
	<section id="documentLayout">
		<h1><?php echo $this->model->menu[$this->uri->segment(2)]['name']; ?></h1>
		<?php echo $page; ?>
	</section>
	<div id="footerLayout">Footer</div>
</div>