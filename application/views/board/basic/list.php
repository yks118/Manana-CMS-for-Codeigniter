<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="listBasic" class="board">
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-sm-1 hidden-xs"><?php echo lang('text_number'); ?></li>
				<li class="col-xs-7 col-sm-7 col-md-9 col-lg-9"><?php echo lang('text_title'); ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-1"><?php echo lang('text_writer'); ?></li>
				<li class="col-sm-1 col-lg-1 hidden-xs"><?php echo lang('text_hit'); ?></li>
			</ul>
		</li>
		<?php if (empty($list)) { ?>
		<li class="text-center"><?php echo lang('system_not_data'); ?></li>
		<?php } else {
			foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-sm-1 hidden-xs"><?php echo $row['number']; ?></li>
				<li class="col-xs-7 col-sm-7 col-md-9 col-lg-9">
					<a target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'.$row['board_id'].'/'); ?>"><?php echo $row['title']; ?></a>
				</li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-1"><?php echo $row['name']; ?></li>
				<li class="col-sm-1 col-lg-1 hidden-xs"><?php echo number_format($row['hit']); ?></li>
			</ul>
		</li>
			<?php }
		} ?>
	</ul>
	
	<div class="text-right">
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/write/'); ?>"><?php echo lang('text_add'); ?></a>
	</div>
	
	<div class="text-center"><?php echo $pagination; ?></div>
	
	<?php
	$attributes = array('name'=>'fsearch','id'=>'fsearch','target'=>'_self','method'=>'get');
	echo form_open_multipart(base_url('/'.$this->model->now_menu['uri'].'/'),$attributes);
	?>
	
	<div class="text-center form-inline">
		<div class="input-group">
			<select class="form-control" name="field">
				<option value="title"><?php echo lang('text_title'); ?></option>
				<option value="document"><?php echo lang('text_document'); ?></option>
				<option value="name"><?php echo lang('text_writer'); ?></option>
			</select>
			<input type="text" class="form-control" name="keyword" id="keyword" required="required" value="<?php echo $this->input->get('keyword'); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
			</span>
		</div>
	</div>
	
	<?php echo form_close(); ?>
</section>