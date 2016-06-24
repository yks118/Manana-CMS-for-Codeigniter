<section id="listMemberAdmin" class="admin">
	<p>Total : <?php echo number_format($total); ?></p>
	
	<ul class="list-unstyled list-table list-table-hover">
		<li class="container-fluid">
			<ul class="list-inline list-table-th row">
				<li class="text-center col-xs-2 col-sm-1">No</li>
				<li class="col-xs-3 col-sm-3 col-md-3 col-lg-4">Username</li>
				<li class="col-xs-4 col-sm-3 col-md-4 col-lg-4">Name</li>
				<li class="text-center hidden-xs col-sm-2 col-md-2 col-lg-1">Join Date</li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center"></li>
			</ul>
		</li>
		<?php foreach ($list as $row) { ?>
		<li class="container-fluid">
			<ul class="list-inline list-table-td row">
				<li class="text-center col-xs-2 col-sm-1"><?php echo $row['number']; ?></li>
				<li class="col-xs-3 col-sm-3 col-md-3 col-lg-4"><?php echo $row['username']; ?></li>
				<li class="col-xs-4 col-sm-3 col-md-4 col-lg-4"><?php echo $row['name']; ?></li>
				<li class="text-center hidden-xs col-sm-2 col-md-2 col-lg-1"><?php echo datetime($row['write_datetime']); ?></li>
				<li class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center">
					<a class="btn btn-primary btn-xs" target="_self" href="<?php echo base_url('/admin/member/update/'.$row['id'].'/'); ?>">
						<span class="hidden-xs">Update</span>
						<i class="fa fa-pencil fa-fw visible-xs"></i>
					</a>
					<a class="btn btn-danger btn-xs <?php echo ($this->member->check_admin($row['id']))?'disabled':''; ?>" target="hIframe" href="">
						<span class="hidden-xs">Delete</span>
						<i class="fa fa-trash fa-fw visible-xs"></i>
					</a>
				</li>
			</ul>
		</li>
		<?php } ?>
	</ul>
</section>