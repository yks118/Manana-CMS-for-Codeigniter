<section id="analyticsAdmin" class="admin">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('text_search'); ?></div>
		<div class="panel-body">
			<?php
			$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'_self','method'=>'get');
			echo form_open_multipart(base_url('/admin/analytics/'),$attributes);
			?>
			
			<div class="form-group">
				<label for="view_id">View ID</label>
				<input type="text" class="form-control" name="view_id" id="view_id" required="required" value="<?php echo (isset($analytics_data['view_id']))?$analytics_data['view_id']:''; ?>" />
			</div>
			
			<div class="form-group">
				<?php foreach ($reports as $key => $value) { ?>
				<label class="radio-inline">
					<input type="radio" name="report" id="report" value="<?php echo $key; ?>" <?php echo ($report == $key)?'checked="checked"':''; ?> />
					<?php echo lang($value); ?>
				</label>
				<?php } ?>
			</div>
			
			<div class="input-group">
				<input type="text" class="form-control datepicker" name="startData" id="startData" value="<?php echo $start_data; ?>" />
				<span class="input-group-addon">~</span>
				<input type="text" class="form-control datepicker" name="endData" id="endData" value="<?php echo $end_data; ?>" />
				<div class="input-group-btn">
					<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
				</div>
			</div>
			
			<?php echo form_close(); ?>
		</div>
	</div>
	
	<?php if (isset($data['browser'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_browser'); ?></div>
		<div class="panel-body">
			<div id="browserChart" class="flotChart"></div>
		</div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th>Browser / Version</th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['browserVersion'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo implode(' / ',$row['dimensions']); ?></td>
					<td class="text-center"><?php echo $row['data']; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['country'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_country'); ?></div>
		<div class="panel-body">
			<div id="countryChart" class="flotChart"></div>
		</div>
	</div>
	<?php } ?>
	
	<?php if (isset($data['deviceCategory'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_device_category'); ?></div>
		<div class="panel-body">
			<div id="deviceCategoryChart" class="flotChart"></div>
		</div>
	</div>
	<?php } ?>
	
	<?php if (isset($data['page'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_favourite_page'); ?></div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th>Url</th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['page'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo auto_link(prep_url($row['dimensions'][0].$row['dimensions'][1]),TRUE,TRUE); ?></td>
					<td class="text-center"><?php echo number_format($row['data']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['keyword'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('text_keyword'); ?></div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th><?php echo lang('text_keyword'); ?></th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['keyword'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo $row['dimensions'][0]; ?></td>
					<td class="text-center"><?php echo number_format($row['data']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['referral'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_referral'); ?></div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th>Url</th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['referral'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo auto_link(prep_url($row['dimensions'][0].$row['dimensions'][1]),TRUE,TRUE); ?></td>
					<td class="text-center"><?php echo number_format($row['data']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['mobileDeviceInfo'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_mobile_device_model'); ?></div>
		<div class="panel-body">
			<div id="mobileDeviceChart" class="flotChart"></div>
		</div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th><?php echo lang('analytics_mobile_device_model'); ?></th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['mobileDeviceInfo'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo $row['dimensions'][0]; ?></td>
					<td class="text-center"><?php echo number_format($row['data']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['browserSize'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_browser_size'); ?></div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_number'); ?></th>
					<th>Browser Size</th>
					<th class="text-center">Data</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['browserSize'] as $key => $row) { ?>
				<tr>
					<td class="text-center"><?php echo $key + 1; ?></td>
					<td><?php echo $row['dimensions'][0]; ?></td>
					<td class="text-center"><?php echo number_format($row['data']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	
	<?php if (isset($data['visitor'])) { ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo lang('analytics_visitors'); ?></div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th class="text-center"><?php echo lang('text_date'); ?></th>
					<th class="text-center"><?php echo lang('analytics_new_visitor'); ?></th>
					<th class="text-center"><?php echo lang('analytics_returning_visitor'); ?></th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$new = $returning = $sum = $total_new = $total_returning = $total_sum = 0;
				foreach ($data['visitor'] as $row) {
					$total = $row['new'] + $row['returning'];
				?>
				<tr>
					<td class="text-center"><?php echo preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})/i','$1-$2-$3',$row['date']); ?></td>
					<td class="text-center">
						<?php
						echo number_format($row['new']);
						
						if ($new < $row['new']) {
							echo '<span class="text-success ml5">(+'.number_format($row['new'] - $new).')</span>';
						} else if ($new > $row['new']) {
							echo '<span class="text-danger ml5">(-'.number_format($new - $row['new']).')</span>';
						}
						?>
					</td>
					<td class="text-center">
						<?php
						echo number_format($row['returning']);
						
						if ($returning < $row['returning']) {
							echo '<span class="text-success ml5">(+'.number_format($row['returning'] - $returning).')</span>';
						} else if ($returning > $row['returning']) {
							echo '<span class="text-danger ml5">(-'.number_format($returning - $row['returning']).')</span>';
						}
						?>
					</td>
					<td class="text-center">
						<?php
						echo number_format($total);
						
						if ($sum < $total) {
							echo '<span class="text-success ml5">(+'.number_format($total - $sum).')</span>';
						} else if ($sum > $total) {
							echo '<span class="text-danger ml5">(-'.number_format($sum - $total).')</span>';
						}
						?>
					</td>
				</tr>
				<?php
					$new = $row['new'];
					$returning = $row['returning'];
					$sum = $total;
					
					$total_new += $row['new'];
					$total_returning += $row['returning'];
					$total_sum += $total;
				}
				?>
				<tr>
					<td class="text-center">Total</td>
					<td class="text-center"><?php echo number_format($total_new); ?></td>
					<td class="text-center"><?php echo number_format($total_returning); ?></td>
					<td class="text-center"><?php echo number_format($total_sum); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php } ?>
</section>

<script type="text/javascript" charset="UTF-8">
<?php if (isset($data['browser'])) { ?>
var browserChart = [
	<?php foreach ($data['browser'] as $key => $row) {
		if ($key) { ?>
	,
		<?php } ?>
	{data: <?php echo $row['data']; ?>, color: '<?php echo $this->model->color[$key]; ?>', label: '<?php echo $row['dimensions'][0]; ?>'}
	<?php } ?>
];
<?php } ?>

<?php if (isset($data['country'])) { ?>
var countryChart = [
	<?php foreach ($data['country'] as $key => $row) {
		if ($key) { ?>
	,
		<?php } ?>
	{data: <?php echo $row['data']; ?>, color: '<?php echo $this->model->color[$key]; ?>', label: '<?php echo $row['dimensions'][0]; ?>'}
	<?php } ?>
];
<?php } ?>

<?php if (isset($data['mobileDeviceBranding'])) { ?>
var mobileDeviceChart = [
	<?php foreach ($data['mobileDeviceBranding'] as $key => $row) {
		if ($key) { ?>
	,
		<?php } ?>
	{data: <?php echo $row['data']; ?>, color: '<?php echo $this->model->color[$key]; ?>', label: '<?php echo $row['dimensions'][0]; ?>'}
	<?php } ?>
];
<?php } ?>

<?php if (isset($data['deviceCategory'])) { ?>
var deviceCategoryChart = [
	<?php foreach ($data['deviceCategory'] as $key => $row) {
		if ($key) { ?>
	,
		<?php } ?>
	{data: <?php echo $row['data']; ?>, color: '<?php echo $this->model->color[$key]; ?>', label: '<?php echo $row['dimensions'][0]; ?>'}
	<?php } ?>
];
<?php } ?>
</script>