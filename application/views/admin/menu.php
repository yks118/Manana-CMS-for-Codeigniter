<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="menuAdmin" class="admin">
	<div class="row">
		<div class="col-sm-6">
			<div class="dd" id="nestable">
				<?php if (isset($list[0])) { ?>
				<ol class="dd-list">
					<li class="dd-item" data-id="0">
						<div class="input-group">
							<div class="input-group-btn dd-handle dd-nodrag">
								<button type="button" class="btn btn-default">
									<i class="fa fa-bars fa-fw"></i>
								</button>
							</div>
							<input type="text" class="form-control dd-content" readonly="readonly" value="<?php echo $this->model->site['name']; ?>" />
							<div class="input-group-btn">
								<button type="button" class="btn btn-primary" onclick="clickMenuAdd(0)"><?php echo lang('text_add'); ?></button>
							</div>
						</div>
						
						<ol class="dd-list">
							<?php foreach ($list as $lnb) { ?>
							<li class="dd-item" data-id="<?php echo $lnb['site_menu_id']; ?>">
								<div class="input-group">
									<div class="input-group-btn dd-handle">
										<button type="button" class="btn btn-default">
											<i class="fa fa-bars fa-fw"></i>
										</button>
									</div>
									<input type="text" class="form-control dd-content" readonly="readonly" value="<?php echo $lnb['name']; ?>" />
									<div class="input-group-btn">
										<?php if ($lnb['is_main'] == 'f') { ?>
										<button type="button" class="btn btn-info" onclick="clickMenuHome(<?php echo $lnb['site_menu_id']; ?>)">Home</button>
										<?php } ?>
										<button type="button" class="btn btn-primary" onclick="clickMenuAdd(<?php echo $lnb['site_menu_id']; ?>)"><?php echo lang('text_add'); ?></button>
										<button type="button" class="btn btn-default" onclick="clickMenuUpdate(<?php echo $lnb['site_menu_id']; ?>)"><?php echo lang('text_update'); ?></button>
										<button type="button" class="btn btn-danger"><?php echo lang('text_delete'); ?></button>
									</div>
								</div>
								
								<?php if (isset($lnb['children'][0])) { ?>
								<ol class="dd-list">
									<?php foreach ($lnb['children'] as $snb) { ?>
									<li class="dd-item" data-id="<?php echo $snb['site_menu_id']; ?>">
										<div class="input-group">
											<div class="input-group-btn dd-handle">
												<button type="button" class="btn btn-default">
													<i class="fa fa-bars fa-fw"></i>
												</button>
											</div>
											<input type="text" class="form-control dd-content" readonly="readonly" value="<?php echo $snb['name']; ?>" />
											<div class="input-group-btn">
												<?php if ($snb['is_main'] == 'f') { ?>
												<button type="button" class="btn btn-info" onclick="clickMenuHome(<?php echo $snb['site_menu_id']; ?>)">Home</button>
												<?php } ?>
												<button type="button" class="btn btn-default" onclick="clickMenuUpdate(<?php echo $snb['site_menu_id']; ?>)"><?php echo lang('text_update'); ?></button>
												<button type="button" class="btn btn-danger"><?php echo lang('text_delete'); ?></button>
											</div>
										</div>
									</li>
									<?php } ?>
								</ol>
								<?php } ?>
							</li>
							<?php } ?>
						</ol>
					</li>
				</ol>
				<?php } else { ?>
				<p class="text-center"><?php echo lang('system_not_data'); ?></p>
				<?php } ?>
			</div>
		</div>
		<div class="col-sm-6">
			<?php
			$attributes = array('name'=>'fwrite','id'=>'fwrite','target'=>'hIframe');
			echo form_open_multipart(base_url('/admin/updateMenuForm/'),$attributes);
			?>
			
			<input type="hidden" name="menu_site_menu_id" id="menu_site_menu_id" value="" />
			<input type="hidden" name="menu_language" id="menu_language" value="<?php echo $this->config->item('language'); ?>" />
			<input type="hidden" name="menu_parent_id" id="menu_parent_id" value="0" />
			
			<div class="form-group">
				<label for="menu_name"><?php echo lang('text_name'); ?></label>
				<input type="text" class="form-control" required="required" name="menu_name" id="menu_name" maxlength="255" value="" />
			</div>
			
			<div class="form-group">
				<label for="menu_uri">Uri</label>
				<input type="text" class="form-control" required="required" name="menu_uri" id="menu_uri" maxlength="255" value="" />
			</div>
			
			<div class="form-group">
				<label for="menu_model"><?php echo lang('text_model'); ?></label>
				<select class="form-control" name="menu_model" id="menu_model" onchange="changeMenuModel()">
					<?php foreach ($model_list as $key => $value) { ?>
					<option value="<?php echo $value; ?>"><?php echo lang('text_'.$value); ?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="form-group">
				<label for="menu_model_id"><?php echo lang('text_model_id'); ?></label>
				<select class="form-control" name="menu_model_id" id="menu_model_id"></select>
			</div>
			
			<div class="form-group">
				<label for="menu_href"><?php echo lang('text_link'); ?></label>
				<input type="text" class="form-control" maxlength="255" name="menu_href" id="menu_href" value="" />
			</div>
			
			<div class="form-group">
				<label for="menu_target"><?php echo lang('text_link_target'); ?></label>
				<select class="form-control" name="menu_target" id="menu_target">
					<option value="_self"><?php echo lang('text_link_target_self'); ?></option>
					<option value="_blank"><?php echo lang('text_link_target_blank'); ?></option>
				</select>
			</div>
			
			<div class="form-group">
				<label for="menu_layout">Layout</label>
				<select class="form-control" name="menu_layout" id="menu_layout">
					<?php foreach ($layout_list as $layout) {
						if ($layout != 'admin') { ?>
					<option value="<?php echo $layout; ?>"><?php echo $layout; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			
			<div class="form-group">
				<label for="grade"><?php echo lang('text_use_grade'); ?></label>
				<div>
					<?php foreach ($site_member_grade_list as $row) { ?>
					<label class="checkbox-inline">
						<input type="checkbox" id="grade_<?php echo $row['id']; ?>" name="grade[]" value="<?php echo $row['id']; ?>"
							checked="checked"
						/>
						<?php echo $row['name']; ?>
					</label>
					<?php } ?>
				</div>
			</div>
			
			<div class="text-right">
				<button type="submit" class="btn btn-primary">Submit</button>
				<button type="button" class="btn btn-danger">Delete</button>
			</div>
			
			<?php echo form_close(); ?>
		</div>
	</div>
</section>