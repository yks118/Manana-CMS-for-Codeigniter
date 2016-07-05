<section id="listPageAdmin" class="admin">
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1">No</li>
				<li class="col-xs-3 col-sm-3 col-md-3 col-lg-9">Title</li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center"></li>
			</ul>
		</li>
		<?php if (count($list)) {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li></li>
			</ul>
		</li>
			<?php }
		} else { ?>
		<li class="text-center">No Data.</li>
		<?php } ?>
	</ul>
	
	<div class="text-right">
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/admin/page/write/'); ?>"><?php echo lang('text_add'); ?></a>
	</div>
</section>