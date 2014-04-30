<?php

class PiLogin_Sidebar_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
				'PiLoginSidebarWidget', // Base ID
				'PiLogin Sidebar Widget', // Name
				array( 'description' => 'PiLogin Sidebar Widget' ) // Args
		);
	}
	public function widget() {
		?>
		<div class="pilogin-links">
			<?php do_action('pi-login-widget'); ?>
		</div>
		<?php
	}

}
