<?php
use App\Entities\Migrations;
?>

<div class="card">
	<div class="card-header">
		<h4 class="card-title">Migration Hitstory</h4>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table tablesorter">
				<thead class="text-primary">
					<tr>
						<th>id</th>
						<th>version</th>
						<th>class</th>
						<th>group</th>
						<th>namespace</th>
						<th>time</th>
						<th>batch</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($list) && is_array($list))
					{
						/** @var Migrations $eMigrations */
						foreach ($list as $eMigrations)
						{
							?>
					<tr>
						<td data-title="id"><?php echo $eMigrations->id; ?></td>
						<td data-title="version"><?php echo $eMigrations->version; ?></td>
						<td data-title="class"><?php echo $eMigrations->class; ?></td>
						<td data-title="group"><?php echo $eMigrations->group; ?></td>
						<td data-title="namespace"><?php echo $eMigrations->namespace; ?></td>
						<td data-title="time"><?php echo $eMigrations->time->format('c'); ?></td>
						<td data-title="batch"><?php echo $eMigrations->batch; ?></td>
					</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>