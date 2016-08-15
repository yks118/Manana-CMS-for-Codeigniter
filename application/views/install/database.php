<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section id="databaseInstall" class="install">
	<div class="container mt30">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title">Database Setting</h3>
			</div>
			<div class="panel-body">
				<p>
					<?php
					switch ($this->config->item('language')) {
						case 'korean' :
								?>
					/application/config/database.php 파일의 username, password, database, dbprefix의 값을 확인해주세요.<br />
					dbprefix의 경우는 ci_가 기본값입니다.
								<?php
							break;
						case 'japanese' :
								?>
					/application/config/database.phpのusername、password、database、dbprefixを確認してください。<br />
					dbprefixの場合、ci_がDefaultです。
								<?php
							break;
						default :
								?>
					Please check.<br />
					username, password, database, dbprefix for /application/config/database.php<br />
					The dbprefix is default value ci_.
								<?php
							break;
					}
					?>
				</p>
				
				<div class="text-right">
					<a class="btn btn-primary" target="_self" href="<?php echo base_url('/install/'); ?>"><?php echo lang('text_submit'); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>