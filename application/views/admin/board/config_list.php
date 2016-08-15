<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="listConfigBoardAdmin" class="admin">
	<p>Total : <?php echo number_format($total); ?> / <?php echo number_format($total_config); ?></p>
	
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo lang('text_number'); ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9"><?php echo lang('text_name'); ?></li>
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
					<a class="btn btn-primary btn-xs" target="_self" href="<?php echo base_url('/admin/board/updateConfig/'.$row['board_config_id'].'/'); ?>">
						<span class="hidden-xs"><?php echo lang('text_update'); ?></span>
						<i class="fa fa-pencil fa-fw visible-xs"></i>
					</a>
					<button class="btn btn-danger btn-xs" onclick="clickConfigDelete(<?php echo $row['id']; ?>)">
						<span class="hidden-xs"><?php echo lang('text_delete'); ?></span>
						<i class="fa fa-trash fa-fw visible-xs"></i>
					</button>
				</li>
			</ul>
		</li>
			<?php }
		} else { ?>
		<li class="text-center">No Data.</li>
		<?php } ?>
	</ul>
	
	<div class="text-right">
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/admin/board/writeConfig/'); ?>"><?php echo lang('text_add'); ?></a>
	</div>
	
	<div class="text-center"><?php echo $pagination; ?></div>
	
	<?php
	$attributes = array('name'=>'fsearch','id'=>'fsearch','target'=>'_self','method'=>'get');
	echo form_open_multipart(base_url('/admin/board/config/'),$attributes);
	?>
	
	<div class="text-center form-inline">
		<div class="input-group">
			<input type="text" class="form-control" name="keyword" id="keyword" required="required" value="<?php echo $this->input->get('keyword'); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
			</span>
		</div>
	</div>
	
	<?php echo form_close(); ?>
</section>