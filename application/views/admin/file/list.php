<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="listFileAdmin" class="admin">
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo lang('text_number'); ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9"><?php echo lang('text_name'); ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center"></li>
			</ul>
		</li>
		<?php if (count($list)) {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo $row['number']; ?></li>
				<li class="col-xs-7 col-sm-8 col-md-9 col-lg-9">
					<a target="_blank" href="<?php echo html_path($row['path']); ?>"><?php echo $row['name']; ?></a>
				</li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center">
					<a target="hIframe" class="btn btn-default btn-xs" href="<?php echo base_url('/file/download/'.$row['id'].'/'); ?>"><?php echo lang('text_file_download'); ?></a>
					<button type="button" class="btn btn-danger btn-xs" onclick="clickFileDelete(this,<?php echo $row['id']; ?>,'refresh')"><?php echo lang('text_delete'); ?></button>
				</li>
			</ul>
		</li>
			<?php }
		} else { ?>
		<li class="text-center">No Data.</li>
		<?php } ?>
	</ul>
	
	<div class="text-center"><?php echo $pagination; ?></div>
	
	<?php
	$attributes = array('name'=>'fsearch','id'=>'fsearch','target'=>'_self','method'=>'get');
	echo form_open_multipart(base_url('/admin/file/'),$attributes);
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