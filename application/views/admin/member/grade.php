<section id="gradeMemberAdmin" class="admin">
	<ul class="list-inline">
		<?php foreach ($list as $row) { ?>
		<li>
			<?php
			$attributes = array('name'=>'fwrite','target'=>'hIframe');
			echo form_open_multipart(base_url('/admin/member/updateGradeForm/'),$attributes);
			?>
			
			<input type="hidden" name="grade_site_member_grade_id" value="<?php echo (empty($row['site_member_grade_id']))?$row['id']:$row['site_member_grade_id']; ?>" />
			<input type="hidden" name="grade_default" value="<?php echo $row['default']; ?>" />
			<input type="hidden" name="grade_language" value="<?php echo $this->config->item('language'); ?>" />
			
			<div class="input-group">
				<input type="text" class="form-control" name="grade_name" value="<?php echo $row['name']; ?>" />
				<div class="input-group-btn">
					<button class="btn btn-primary" type="submit"><?php echo lang('text_update'); ?></button>
					
					<?php if (
						(
							($this->model->site['admin_grade_id'] != $row['id']) ||
							($row['site_member_grade_id'] != 0 && $this->model->site['admin_grade_id'] != $row['site_member_grade_id'])
						) &&
						($row['default'] == 'f')
					) { ?>
					<button class="btn btn-default" type="button" onclick="clickDefault(this.form)"><?php echo lang('member_grade_default'); ?></button>
					<button class="btn btn-danger" type="button" onclick="clickDelete(this.form)"><?php echo lang('text_delete'); ?></button>
					<?php } ?>
				</div>
			</div>
			
			<?php echo form_close(); ?>
		</li>
		<?php } ?>
		
		<li>
			<?php
			$attributes = array('name'=>'fwrite','target'=>'hIframe');
			echo form_open_multipart(base_url('/admin/member/writeGradeForm/'),$attributes);
			?>
			
			<div class="input-group">
				<input type="text" class="form-control" name="grade_name" required="required" value="" />
				<div class="input-group-btn">
					<button type="submit" class="btn btn-primary"><?php echo lang('text_submit'); ?></button>
				</div>
			</div>
			
			<?php echo form_close(); ?>
		</li>
	</ul>
</section>