<?php

new Conferencer_Settings_Cache();
class Conferencer_Settings_Cache {
	function __construct() {
		register_activation_hook(CONFERENCER_REGISTER_FILE, array(&$this, 'activate'));
		add_action('admin_init', array(&$this, 'save'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}
	
	function activate() {
		update_option('conferencer_caching', true);
	}
	
	function admin_menu() {
		add_submenu_page(
			'conferencer',
			"Cache",
			"Cache",
			'edit_posts',
			'conferencer_cache',
			array(&$this, 'page')
		);
	}
	
	function page() {
		if (!current_user_can('edit_posts')) wp_die("You do not have sufficient permissions to access this page.");
		?>
		
		<div id="conferencer_cache" class="wrap">
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
				<?php wp_nonce_field('nonce_conferencer_cache'); ?>
				
				<h2>Conferencer Caching</h2>
				<p>Conferencer caches the content of any of it's shortcodes you use in your site.</p>

				<p>
					<?php if (get_option('conferencer_caching')) { ?>
						<input type="submit" name="conferencer_disable_cache" value="Disable Caching" />
					<?php } else { ?>
						<input type="submit" name="conferencer_enable_cache" value="Enable Caching" />
					<?php } ?>
					<input type="submit" name="conferencer_clear_cache" value="Clear Cache" />
				</p>

				<input type="hidden" name="conferencer_cache_settings" value="save" />
			</form>
		</div>
		
		<?php
	}
	
	function save() {
		if (isset($_POST['conferencer_cache_settings']) && check_admin_referer('nonce_conferencer_cache')) {
			if (isset($_POST['conferencer_disable_cache'])) {
				update_option('conferencer_caching', false);
				Conferencer::add_admin_notice("Caching disabled.");
			} else if (isset($_POST['conferencer_enable_cache'])) {
				update_option('conferencer_caching', true);
				Conferencer::add_admin_notice("Caching enabled.");
			} else if (isset($_POST['conferencer_clear_cache'])) {
				Conferencer_Shortcode::clear_cache();
				Conferencer::add_admin_notice("Cach cleared.");
			}
			
			header("Location: ".$_SERVER['REQUEST_URI']);
			die;
		}
	}
}