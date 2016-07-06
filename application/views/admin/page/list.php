<?php
$this->model->js($path.'/js/page.js');
?>

<section id="listPageAdmin" class="admin">
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo lang('text_number'); ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9"><?php echo lang('text_title'); ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center"></li>
			</ul>
		</li>
		<?php if (count($list)) {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo $row['number']; ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9"><?php echo $row['title']; ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center">
					<a class="btn btn-primary btn-xs" target="_self" href="<?php echo base_url('/admin/page/update/'.$row['id'].'/'); ?>"><?php echo lang('text_update'); ?></a>
					<button type="button" class="btn btn-danger btn-xs" onclick="clickPageDelete(<?php echo $row['id']; ?>)"><?php echo lang('text_delete'); ?></button>
				</li>
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