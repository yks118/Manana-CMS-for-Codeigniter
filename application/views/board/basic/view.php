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
	
	<div class="text-right">
		<a class="btn btn-default" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/'); ?>"><?php echo lang('text_list'); ?></a>
		<?php if ($this->board->auth['update']) { ?>
		<a class="btn btn-primary" target="_self" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/update/'.$data['board_id'].'/'); ?>"><?php echo lang('text_update'); ?></a>
		<?php } ?>
		<?php if ($this->board->auth['delete']) { ?>
		<a class="btn btn-danger" target="hIframe" href="<?php echo base_url('/'.$this->model->now_menu['uri'].'/delete/'.$data['board_id'].'/'); ?>"><?php echo lang('text_delete'); ?></a>
		<?php } ?>
	</div>
</section>