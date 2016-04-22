<section id="completeInstall" class="install">
	<div class="container mt30">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Install Complete</h3>
			</div>
			<div class="panel-body">
				<p>
					<?php
					switch ($this->config->item('language')) {
						case 'korean' :
								?>
					Manana CMS가 설치되었습니다.
								<?php
							break;
						case 'japanese' :
								?>
					Manana CMSがインストール完了しました。
								<?php
							break;
						default :
								?>
					Manana CMS Install Complate.
								<?php
							break;
					}
					?>
				</p>
				
				<div class="text-right">
					<a class="btn btn-primary" target="_self" href="<?php echo base_url('/admin/'); ?>"><?php echo lang('text_submit'); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>