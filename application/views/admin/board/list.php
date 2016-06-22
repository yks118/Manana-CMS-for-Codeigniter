<section id="listBoardAdmin" class="admin">
	<p>Total : <?php echo number_format($total); ?> / <?php echo number_format($total_config); ?></p>
	
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1">No</li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9">Title</li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2"></li>
			</ul>
		</li>
		<?php if (count($list)) {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo $row['number']; ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9"><?php echo $row['name']; ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center">
					<a class="btn btn-primary btn-xs" target="_self" href="<?php echo base_url('/admin/board/update/'.$row['id'].'/'); ?>">
						<span class="hidden-xs">Update</span>
						<i class="fa fa-pencil fa-fw visible-xs"></i>
					</a>
					<a class="btn btn-danger btn-xs" target="hIframe" href="<?php echo base_url('/admin/board/delete/'.$row['id'].'/'); ?>">
						<span class="hidden-xs">Delete</span>
						<i class="fa fa-trash fa-fw visible-xs"></i>
					</a>
				</li>
			</ul>
		</li>
			<?php }
		} else { ?>
		<li class="text-center">No Data.</li>
		<?php } ?>
	</ul>
	
	<div class="text-right">
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/admin/board/write/'); ?>"><?php echo lang('text_add'); ?></a>
	</div>
</section>