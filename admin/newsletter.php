<?php class pwaforwp_ads_newsletter {
    
	const DISPLAY_VERSION = 'v1.0';
        
	function __construct () {
		add_action('admin_enqueue_scripts', array($this, 'pwaforwp_admin_enqueue_scripts'));
	}
        
	function pwaforwp_admin_enqueue_scripts () { 
            
		$dismissed = explode (',', get_user_meta (wp_get_current_user ()->ID, 'dismissed_wp_pointers', true));                
		$do_tour = !in_array ('pwaforwp_subscribe_pointer', $dismissed);   
                
		if ($do_tour) {
			wp_enqueue_style ('wp-pointer');
			wp_enqueue_script ('wp-pointer');
			add_action('admin_print_footer_scripts', array($this, 'pwaforwp_admin_print_footer_scripts'));
			add_action('admin_head', array($this, 'pwaforwp_admin_head'));  // Hook to admin head
		}
	}
        
	function pwaforwp_admin_head () {           
		?>
		<style type="text/css" media="screen"> #pointer-primary { margin: 0 5px 0 0; } </style>
		<?php }
	function pwaforwp_admin_print_footer_scripts () {
             
		global $pagenow;                                
		global $current_user;                
		$tour = array ();
                $tab  = isset($_GET['tab']) ? $_GET['tab'] : '';                   
		$function = '';
		$button2 = '';
		$options = array ();
		$show_pointer = false;

         if (!array_key_exists($tab, $tour)) {                
			$show_pointer = true;                                            
			$displayID = '#toplevel_page_pwaforwp';  // Define ID used on page html element where we want to display pointer
			$content = '<h3>' . sprintf (esc_html__('You are awesome for using PWA for WP!', 'pwa-for-wp'), self::DISPLAY_VERSION) . '</h3>';
			$content .= sprintf('<p>%s <b>%s</b> %s</p>', esc_html__('Do you want the latest on','pwa-for-wp'),esc_html__('PWA update', 'pwa-for-wp'), esc_html__('before others and some best resources on monetization in a single email? - Free just for users of PWA!','pwa-for-wp'));
                        $content .= __('
                        <style type="text/css">
                        .wp-pointer-buttons{ padding:0; overflow: hidden; }
                        .wp-pointer-content .button-secondary{  left: -25px;background: transparent;top: 10px; border: 0;position: relative; padding: 0; box-shadow: none;margin: 0;color: #0085ba;} .wp-pointer-content .button-primary{ display:none}	#pwaforwp_mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
                        </style>
                        <div id="pwaforwp_mc_embed_signup">  
                        <form class="ml-block-form" action="https://app.mailerlite.com/webforms/submit/v5p5a5" data-code="v5p5a5" method="post" target="_blank">
                                    <div id="pwaforwp_mc_embed_signup_scroll">
                                    <div class="pwaforwp-mc-field-group" style="    margin-left: 15px;    width: 195px;    float: left;">
                                                    <input type="text" name="fields[name]" class="form-control" placeholder="Name" hidden value="' . esc_attr( $current_user->display_name ) . '" style="display:none">

                                                    <input type="text" value="' . esc_attr( $current_user->user_email ) . '" name="fields[email]" class="form-control" placeholder="Email*"  style="      width: 180px;    padding: 6px 5px;">

                                                    <input type="text" name="fields[company]" class="form-control" placeholder="Website" hidden style=" display:none; width: 168px; padding: 6px 5px;" value="' . esc_url( get_home_url() ) . '">
                                                        
                                                    <input type="hidden" name="ml-submit" value="1" />
                                    </div>
                                    <div id="mce-responses">
                                            <div class="response" id="mce-error-response" style="display:none"></div>
                                            <div class="response" id="mce-success-response" style="display:none"></div>
                                    </div>
                                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a631df13442f19caede5a5baf_c9a71edce6" tabindex="-1" value=""></div>
                                            <input type="submit" value="Subscribe" name="subscribe" id="pointer-close" class="button mc-newsletter-sent" style=" background: #0085ba; border-color: #006799; padding: 0px 16px; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; height: 30px; margin-top: 1px; color: #fff; box-shadow: 0 1px 0 #006799;">
                                    </div>
                            </form>
                        </div>','pwa-for-wp');
                                    $options = array (
                                            'content' => $content,
                                            'position' => array ('edge' => 'left', 'align' => 'left')
                                            );
		}
		if ($show_pointer) {
			$this->pwaforwp_pointer_script ($displayID, $options, esc_html__('No Thanks', 'pwa-for-wp'), $button2, $function);
		}
	}
	function pwaforwp_get_admin_url($page, $tab) {
		$url = admin_url();
		$url .= $page.'?tab='.$tab;
		return $url;
	}
	function pwaforwp_pointer_script ($displayID, $options, $button1, $button2=false, $function='') {
		?>
		<script type="text/javascript">
			(function ($) {
				var wp_pointers_tour_opts = <?php echo json_encode ($options); ?>, setup;
				wp_pointers_tour_opts = $.extend (wp_pointers_tour_opts, {
					buttons: function (event, t) {
						button= jQuery ('<a id="pointer-close" class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');
						button_2= jQuery ('#pointer-close.button');
						button.bind ('click.pointer', function () {
							t.element.pointer ('close');
						});
						button_2.on('click', function() {
							t.element.pointer ('close');
						} );
						return button;
					},
					close: function () {
						$.post (ajaxurl, {
							pointer: 'pwaforwp_subscribe_pointer',
							action: 'dismiss-wp-pointer'
						});
					},
                                        show: function(event, t){                                           
                                         t.pointer.css({'left':'170px', 'top':'400px', 'position':'fixed'});
                                      }                                               
				});
				setup = function () {
					$('<?php echo esc_attr($displayID); ?>').pointer(wp_pointers_tour_opts).pointer('open');
					<?php if ($button2) { ?>
						jQuery ('#pointer-close').after ('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
						jQuery ('#pointer-primary').click (function () {
							<?php echo $function; ?>
						});
						jQuery ('#pointer-close').click (function () {
							$.post (ajaxurl, {
								pointer: 'pwaforwp_subscribe_pointer',
								action: 'dismiss-wp-pointer'
							});
						})
					<?php } ?>
				};
				if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {
					$(window).bind('load.wp-pointers', setup);
				}
				else {
					setup ();
				}
			}) (jQuery);
		</script>
 	<?php
	}
}
$pwaforwp_ads_newsletter = new pwaforwp_ads_newsletter();
?>