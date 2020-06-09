<?php
/**
 * Black Dashboard
 *
 * @url https://github.com/creativetimofficial/black-dashboard
 *
 * @license MIT
 */
?>
<div class="wrapper">
	<div class="sidebar">
		<div class="sidebar-wrapper">
			<ul class="nav">
				<li class="<?php echo \Config\Services::request()->uri->getSegment(1) === 'db-migration'?'active':''; ?>">
					<a href="<?php echo site_url('db-migration'); ?>">
						<i class="tim-icons icon-settings"></i>
						<p>DB Migration</p>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="main-panel">
		<!-- Navbar -->
		<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
			<div class="container-fluid">
				<div class="navbar-wrapper">
					<div class="navbar-toggle d-inline">
						<button type="button" class="navbar-toggler">
							<span class="navbar-toggler-bar bar1"></span>
							<span class="navbar-toggler-bar bar2"></span>
							<span class="navbar-toggler-bar bar3"></span>
						</button>
					</div>
					<a
						class="navbar-brand"
						href="<?php echo site_url(); ?>"
					><?php echo \Config\Services::html()->getSiteName(); ?></a>
				</div>

				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-bar navbar-kebab"></span>
					<span class="navbar-toggler-bar navbar-kebab"></span>
					<span class="navbar-toggler-bar navbar-kebab"></span>
				</button>
				<div class="collapse navbar-collapse" id="navigation">
					<ul class="navbar-nav ml-auto">
						<li class="dropdown nav-item">
							<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
								<span>
									<?php
									if (account_is_login())
										echo empty(account()->name)?account()->username:account()->name;
									else
										echo 'Account';
									?>
									&nbsp;
								</span>
								<b class="caret d-none d-lg-block d-xl-block"></b>
							</a>
							<ul class="dropdown-menu dropdown-navbar">
								<?php
								if (account_is_login())
								{
									?>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/setting'); ?>"
									>Setting</a>
								</li>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/change-password'); ?>"
									>Change Password</a>
								</li>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/leave'); ?>"
									>Leave</a>
								</li>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/sign-out'); ?>"
									>Sign Out</a>
								</li>
									<?php
								}
								else
								{
									?>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/sign-in'); ?>"
									>Sign In</a>
								</li>
								<li class="nav-link">
									<a
										class="nav-item dropdown-item"
										href="<?php echo site_url('account/sign-up'); ?>"
									>Sign Up</a>
								</li>
									<?php
								}
								?>
							</ul>
						</li>
						<li class="separator d-lg-none"></li>
					</ul>
				</div>
			</div>
		</nav>
		<!-- End Navbar -->

		<div class="content"><?php /** @var string $page */ echo $page??''; ?></div>

		<footer class="footer">
			<div class="container-fluid">
				<ul class="nav">
					<li class="nav-item">
						<a href="https://manana.kr" class="nav-link">Manana</a>
					</li>
				</ul>

				<div class="copyright">
					Copyright © <?php echo date('Y'); ?>
					<a target="_blank" href="http://pureani.tistory.com/">PureAni</a>
					/
					Designed by
					<a
						target="_blank" rel="noopener noreferrer"
						href="https://github.com/creativetimofficial/black-dashboard"
					>Creative Tim</a>
				</div>
			</div>
		</footer>
	</div>
</div>
