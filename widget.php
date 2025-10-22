<?php
if (! function_exists('deandev_add_dashboard_widgets')) {

	add_action('wp_dashboard_setup', 'deandev_add_dashboard_widgets');
	function deandev_add_dashboard_widgets()
	{
		add_meta_box('deandev_dashboard_widget', 'ValvePress Items Support', 'deandev_dashboard_widget_function', 'dashboard', 'side', 'high');
	}

	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	function deandev_dashboard_widget_function()
	{


		$purl = plugins_url('', __FILE__);

?>

		<table>

			<tbody>
				<tr>

					<td>
						<img style="float: left; margin-bottom: 20px;" src="<?php echo $purl ?>/images/widget/help.png">
					</td>

					<td>

						<p>
							"The plugin includes free support. Encountering an issue? Don’t worry — we have a dedicated help desk just for you. Submit a <a target="_blank" href="http://deandev.com/me/support">support ticket</a>, and we’ll be happy to assist you. Your satisfaction is our priority."
						</p>
						<p>
							<a href="https://deandev.com/me/support" class="button"> Open a Support Ticket Now </a>
						</p>

					</td>
				</tr>

				<tr>

					<td></td>

					<td>
						<p></p>
						<div class="more-work">
							<div>

								<p>our beloved collection</p>


								<a href="https://1.envato.market/ra0oov"><img width="80" height="80" border="0" alt="Wordpress Rankie Plugin - CodeCanyon Item for Sale" src="<?php echo $purl ?>/images/widget/7605032.jpg" title=""></a> <a href="https://1.envato.market/MdXG2"><img width="80" height="80" border="0" alt="Wordpress Auto Spinner - Articles Rewriter - CodeCanyon Item for Sale" src="<?php echo $purl ?>/images/widget/48892303.jpg" title=""></a> <a href="https://1.envato.market/rqbgD"><img width="80" height="80" border="0" alt="Wordpress Automatic Plugin - CodeCanyon Item for Sale" src="<?php echo $purl ?>/images/widget/26595897.png" title="Wordpress Automatic Plugin"></a> <a
									href="https://1.envato.market/Zjdog"><img width="80" height="80" border="0" alt="Pinterest Automatic Pin Wordpress Plugin - CodeCanyon Item for Sale" src="<?php echo $purl ?>/images/widget/25603958.jpg" title=""></a> <a href="https://1.envato.market/9L9a35"><img width="80" height="80" border="0" alt="Wordpress Keyword Tool Plugin - CodeCanyon Item for Sale" src="<?php echo $purl ?>/images/widget/57630010.jpg" title=""></a>



							</div>
							<div class="clear">
								<!-- -->
							</div>
							<p>
								<small><a href="https://1.envato.market/ZQBonQ">More items by ValvePress</a></small>
								<small>| <a id="wp_valvepress_widget_hide" href="#"> Don't show this widget</a></small>

							</p>
 
						</div>
						<p></p>
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
				</tr>

			</tbody>
		</table>

		<script type="text/javascript">
			jQuery('#wp_valvepress_widget_hide').click(function() {
				jQuery('#deandev_dashboard_widget-hide').trigger('click');
			});
		</script>

<?php
	} // function of the widget
}//function exists