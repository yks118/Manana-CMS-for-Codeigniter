<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->model->css($path.'/css/basic.board.less');
?>

<section id="viewBasic" class="board">
	<div class="headerBoard"><?php echo $data['title']; ?></div>
	<ul class="list-inline text-right">
		<li>
			<i class="fa fa-user fa-fw"></i>
			<?php echo $data['name']; ?>
		</li>
		<li>
			<i class="fa fa-check fa-fw"></i>
			<?php echo number_format($data['hit']); ?>
		</li>
		<li>
			<i class="fa fa-calendar fa-fw"></i>
			<?php echo datetime($data['write_datetime']); ?>
		</li>
	</ul>
	
	<div class="documentBoard"><?php echo $data['document']; ?></div>
	
	<?php if ($data['member_id']) { ?>
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="pull-left mr10">
				<?php if (isset($data['member']['profile_photo'][0])) { ?>
				<img class="profile_photo" src="<?php echo html_path($data['member']['profile_photo'][0]['path']); ?>" alt="<?php echo $data['name']; ?>" />
				<?php } else { ?>
				<img class="profile_photo" src="holder.js/120x120" alt="<?php echo $data['name']; ?>" />
				<?php } ?>
				<div><?php echo $data['member']['name']; ?></div>
			</div>
			<div><?php echo $data['member']['description']; ?></div>
		</div>
	</div>
	<?php } ?>
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'); ?>"><?php echo lang('text_list'); ?></a>
		
		<?php if ($this->board->auth['update']) { ?>
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/update/'.$data['board_id'].'/'); ?>"><?php echo lang('text_update'); ?></a>
		<?php } else if ($data['password']) { ?>
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/password/'.$row['board_id'].'/update/'); ?>"><?php echo lang('text_update'); ?></a>
		<?php } ?>
		
		<?php if ($this->board->auth['delete']) { ?>
		<button type="button" class="btn btn-danger" onclick="clickDelete(<?php echo $data['board_id']; ?>)"><?php echo lang('text_delete'); ?></button>
		<?php } else if ($data['password']) { ?>
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/password/'.$row['board_id'].'/deleteForm/'); ?>"><?php echo lang('text_update'); ?></a>
		<?php } ?>
	</div>
</section>