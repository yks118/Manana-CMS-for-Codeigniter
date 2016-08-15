<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="loginLogMember" class="member">
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1">No</li>
				<li class="col-xs-7 col-sm-8">IP</li>
				<li class="text-center col-xs-1">Status</li>
				<li class="text-center col-xs-2">Date</li>
			</ul>
		</li>
		<?php if (isset($list[0])) {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo number_format($row['number']); ?></li>
				<li class="col-xs-7 col-sm-8"><?php echo $row['ip']; ?></li>
				<li class="text-center col-xs-1">
					<?php if ($row['status'] == 't') { ?>
					<span class="text-success">
						<i class="fa fa-circle-o fa-fw"></i>
					</span>
					<?php } else { ?>
					<span class="text-danger">
						<i class="fa fa-times fa-fw"></i>
					</span>
					<?php } ?>
				</li>
				<li class="text-center col-xs-2"><?php echo datetime($row['write_datetime']); ?></li>
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
	echo form_open_multipart(base_url('/member/loginLog/'),$attributes);
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