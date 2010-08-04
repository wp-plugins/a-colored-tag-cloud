<?php
/**
 * Plugin Name: ILWP Colored Tag Cloud
 * Plugin URI: http://ilikewordpress.com/colored-tag-cloud/
 * Description: An expansion of the standard WP tag cloud widget. Adds colors, min/max sizes, and the option to include in template.
 * Version: 1.3
 * Author: Steve Johnson
 * Author URI: http://ilikewordpress.com/
 */

/*  Copyright 2009  Steve Johnson  (email : steve@ilikewordpress.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	define( 'ILWP_CTC_VERSION', 1.2 );
	
	function ilwp_set_defaults() {
		$options = get_option('ilwp_widget_tag_cloud');
		## if options already set, return, this isn't necessary
		if ( $options ) {
			return;
		} else {
			$default_colors = array(	'aqua', 'black', 'blue', 'fuchsia',
										'gray', 'green', 'lime', 'maroon',
										'navy', 'olive', 'purple', 'red',
										'silver', 'teal', 'white', 'yellow');

			$options['color_names'] = $default_colors;
			$options['min_size']			= 8;
			$options['max_size']			= 20;
			$options['use_colors']		= true;
			$options['use_color_names']	= true;
			$options['number']			= 45;
			update_option('ilwp_widget_tag_cloud', $options);
		}
	}
	
	function ilwp_colored_tag_cloud_options_page() {
		
		$options = $newoptions = get_option('ilwp_widget_tag_cloud');
		$default_colors = array(	'aqua', 'black', 'blue', 'fuchsia',
											'gray', 'green', 'lime', 'maroon',
											'navy', 'olive', 'purple', 'red',
											'silver', 'teal', 'white', 'yellow');

		## add some defaults
		if ( ! $options ) :
			$newoptions['color_names'] = $default_colors;
			$newoptions['min_size']			= 8;
			$newoptions['max_size']			= 20;
			$newoptions['use_colors']		= true;
			$newoptions['use_color_names']	= true;
			$newoptions['number']			= 45;
		endif;
		
		## new option for v1.1
		if ( !isset( $newoptions['number']) )
			$newoptions['number'] = 45;
		
		if ( isset( $_POST['ilwp-tag-cloud-submit'] ) && $_POST['ilwp-tag-cloud-submit'] != '' ) {
			$newoptions['min_size']			= strip_tags(stripslashes($_POST['ilwp-tag-cloud-min-size']));
			$newoptions['max_size']			= strip_tags(stripslashes($_POST['ilwp-tag-cloud-max-size']));
			$newoptions['use_colors']		= strip_tags(stripslashes($_POST['ilwp-tag-cloud-colors']));
			$newoptions['use_color_names']	= strip_tags(stripslashes($_POST['ilwp-tag-cloud-color-names']));
			$newoptions['number']			= intval( $_POST['ilwp-tag-cloud-number']);
			## make sure the color list is populated
			if ( $_POST['ilwp-tag-cloud-color-list'] == '' ) :
				$newcolors = $default_colors;
			else :			
				## get color names/numbers into an array
				$str = $_POST['ilwp-tag-cloud-color-list'];
				## replace spaces with pipes
				$str = preg_replace('/\s+/', '|', $str);
				$str = trim( $str, "|" );
				
				## get rid of any hash marks ppl might have put in
				$str = str_replace( '#', '', $str );				
				$newcolors = explode( '|', $str );
			endif;
			$newoptions['color_names'] = $newcolors;
		}
		
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('ilwp_widget_tag_cloud', $options);
		}
		
		$minsize = $options['min_size'];
		$maxsize = $options['max_size'];
		$colors = implode( $options['color_names'], "\r\n" );
?>		
		<div class="wrap">
			<div style="padding: 10px; border: 1px dotted #ccc; width: 250px; float: right; margin-right: 10px; margin-left: 30px; text-align: center;">
				<h3>Like <acronym title="I Like WordPress!">ILWP</acronym> Colored Tag Cloud</h3>
				<h4>Consider making a donation!</h4>
				<p><small>Donations help with the ongoing development and feature additions of <acronym title="I Like WordPress!">ILWP</acronym> Colored Tag Cloud. Thank you!</small></p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick" />
					<input type="hidden" name="hosted_button_id" value="4176561" />
					<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
				</form>
			</div>
			<form method="post" action="">
			<?php wp_nonce_field('update-options'); ?>
				<h2><acronym title="I Like WordPress!">ILWP</acronym> Colored Tag Cloud v. <?php echo ILWP_CTC_VERSION ;?> ~ General Options</h2>
				<p>For more info on the <acronym title="I Like WordPress!">ILWP</acronym> Colored Tag Cloud plugin, please <a href="http://ilikewordpress.com/colored-tag" title="The ILWP Colored Tag Cloud plugin home page">visit the plugin page</a>. Feel free to leave comments or post feature requests.</p>
				<table style="clear: none; width: inherit" class="form-table">
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-number"><?php _e('Display how many tags?') ?><br /><small> suggested: 30-50, default 45</small></label></th>
						<td>
							<input type="text" style="width: 35px;" name="ilwp-tag-cloud-number" value="<?php echo $newoptions['number']; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-colors"><?php _e('Use colors?') ?></label></th>
						<td>
							<select id="ilwp-tag-cloud-colors" name="ilwp-tag-cloud-colors" >
								<option value="1" <?php $selected = ( $newoptions['use_colors'] == true )? 'selected="selected"' : ""; echo $selected; ?>>yes </option>
								<option value="0" <?php $selected = ( $newoptions['use_colors'] == false )? 'selected="selected"' : ""; echo $selected; ?>>no </option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-color-names"><?php _e('Use color numbers or names?') ?></label></th>
						<td>
							<select id="ilwp-tag-cloud-color-names" name="ilwp-tag-cloud-color-names" >
								<option value="1" <?php $selected = ( $newoptions['use_color_names'] == true )? 'selected="selected"' : ""; echo $selected; ?>>names </option>
								<option value="0" <?php $selected = ( $newoptions['use_color_names'] == false )? 'selected="selected"' : ""; echo $selected; ?>>numbers</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-color-list"><?php _e('Color list (one per line):') ?><br /><small>You can use either named colors or hex color numbers, but not both. Do not enter the # mark if you're using numbers. If you're using numbers, you can use the shorthand 3-digit or full-length 6-digit number. Be sure to set the above 'names or numbers' option appropriately, or you will see unexpected results :-)</small></label></th>
						<td>							
							<textarea id="ilwp-tag-cloud-color-list" name="ilwp-tag-cloud-color-list" rows="8" ><?php echo $colors; ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-min-size"><?php _e('Min font size:') ?></label></th>
						<td>							
							<select style="width: 50px;" id="ilwp-tag-cloud-min-size" name="ilwp-tag-cloud-min-size" >
								<option <?php $selected = ($minsize=='6')? 'selected="selected"' : ""; echo $selected; ?>>6 </option>
								<option <?php $selected = ($minsize=='7')? 'selected="selected"' : ""; echo $selected; ?>>7 </option>
								<option <?php $selected = ($minsize=='8')? 'selected="selected"' : ""; echo $selected; ?>>8 </option>
								<option <?php $selected = ($minsize=='9')? 'selected="selected"' : ""; echo $selected; ?>>9 </option>
								<option <?php $selected = ($minsize=='10')? 'selected="selected"' : ""; echo $selected; ?>>10 </option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="ilwp-tag-cloud-max-size"><?php _e('Max font size:') ?></label></th>
						<td>							
							<select style="width: 50px;" id="ilwp-tag-cloud-max-size" name="ilwp-tag-cloud-max-size" >
								<option <?php $selected = ($maxsize=='18')? 'selected="selected"' : ""; echo $selected; ?>>18 </option>
								<option <?php $selected = ($maxsize=='20')? 'selected="selected"' : ""; echo $selected; ?>>20 </option>
								<option <?php $selected = ($maxsize=='22')? 'selected="selected"' : ""; echo $selected; ?>>22 </option>
								<option <?php $selected = ($maxsize=='26')? 'selected="selected"' : ""; echo $selected; ?>>26 </option>
								<option <?php $selected = ($maxsize=='30')? 'selected="selected"' : ""; echo $selected; ?>>30 </option>
							</select>
						</td>
					</tr>
				</table>
				<input type="hidden" name="ilwp-tag-cloud-submit" id="ilwp-tag-cloud-submit" value="1" />
				<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</form>
		</div>
<?php
	} ## end options page

	function ctg_options_page () {
		add_options_page('Colored Tag Cloud Options', 'Colored Tag Cloud', 8, 'colored-tag-cloud/colored-tag-cloud.php', 'ilwp_colored_tag_cloud_options_page');
	}
	

	function ilwp_tag_cloud() {
		$options = get_option('ilwp_widget_tag_cloud');
		
		$args['smallest']	 = $options['min_size'];
		$args['number']		 = $options['number'];
		$args['largest']	 = $options['max_size'];
		$args['colors']		 = $options['color_names'];
		$args['use_colors']	 = ( bool )$options['use_colors'];
		$args['use_names']	 = ( bool )$options['use_color_names'];

		$defaults = array(
			'unit' => 'pt', 'number' => 45,
			'format' => 'flat', 'orderby' => 'count', 'order' => 'RAND',
			'exclude' => '', 'include' => ''
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$tags = get_tags( array_merge( $args, array('orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags
		if ( empty( $tags ) )
			return;
		$return = ilwp_generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args
		if ( is_wp_error( $return ) )
			return false;
		$return = apply_filters( 'ilwp_tag_cloud', $return, $args );
		if ( 'array' == $args['format'] )
			return $return;
		echo $return;
	}

	function ilwp_generate_tag_cloud( $tags, $args = '' ) {
		global $wp_rewrite;

		$defaults = array(
			'unit' => 'pt', 'number' => 45,
			'format' => 'flat', 'orderby' => 'count',
			'order' => 'DESC'
		);

		$args = wp_parse_args( $args, $defaults );
		extract($args);

		if ( !is_array( $tags ) || empty( $tags ) )
			return;

		$counts = $tag_links = array();
		foreach ( (array) $tags as $tag ) {
			$counts[$tag->name] = $tag->count;
			$tag_links[$tag->name] = get_tag_link( $tag->term_id );
			if ( is_wp_error( $tag_links[$tag->name] ) )
				return $tag_links[$tag->name];
			$tag_ids[$tag->name] = $tag->term_id;
		}
		$min_count = min($counts);
		$spread = max($counts) - $min_count;
		if ( $spread <= 0 )
			$spread = 1;
		$font_spread = $largest - $smallest;
		if ( $font_spread <= 0 )
			$font_spread = 1;
		$font_step = $font_spread / $spread;
		// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
		if ( 'name' == $orderby )
			uksort($counts, 'strnatcasecmp');
		else
			asort($counts);
		if ( 'DESC' == $order )
			$counts = array_reverse( $counts, true );
		elseif ( 'RAND' == $order ) {
			$keys = array_rand( $counts, count($counts) );
			foreach ( $keys as $key )
				$temp[$key] = $counts[$key];
			$counts = $temp;
			unset($temp);
		}
		$a = array();
		$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? ' rel="tag"' : '';
		$pre = ( $use_names ) ? '' : '#';
		$c = sizeof( $colors );
		foreach ( $counts as $tag => $count ) {
			$tag_id = $tag_ids[$tag];
			$tag_link = clean_url($tag_links[$tag]);
			if ( $use_colors ) :
				$color = rand( 0, $c );
				$colorstyle = " color: $pre" . $colors[$color] . ";";
			else :
				$colorstyle = "";
			endif;
			$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . attribute_escape( sprintf( __ngettext('%d topic','%d topics',$count), $count ) ) . "'$rel style='font-size: " .
				( $smallest + ( ( $count - $min_count ) * $font_step ) )
				. "$unit;$colorstyle'>$tag</a>";
		}
		switch ( $format ) :
		case 'array' :
			$return =& $a;
			break;
		case 'list' :
			$return = "<ul class='ilwp-tag-cloud'>\n\t<li>";
			$return .= join("</li>\n\t<li>", $a);
			$return .= "</li>\n</ul>\n";
			break;
		default :
			$return = join("\n", $a);
			break;
		endswitch;
		return apply_filters( 'ilwp_generate_tag_cloud', $return, $tags, $args );
	}

	function ilwp_widget_tag_cloud($args) {
		extract($args);
		$options = get_option('ilwp_widget_tag_cloud');
		$title = empty($options['title']) ? __('Tags') : apply_filters('widget_title', $options['title']);
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		ilwp_tag_cloud();
		echo $after_widget . "\n\n";
	}

	function ilwp_widget_tag_cloud_control() {
		$options = $newoptions = get_option('ilwp_widget_tag_cloud');
		
		if ( isset( $_POST['ilwp-tag-cloud-submit'] ) && $_POST['ilwp-tag-cloud-submit'] == 1 ) {
			$newoptions['title'] = strip_tags( stripslashes( attribute_escape($_POST['ilwp-tag-cloud-title'] ) ) );
		}
		
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('ilwp_widget_tag_cloud', $options);
		}
		
		$title = ( !isset( $options['title'] ) || $options['title'] == '' ) ? '' : $options['title'];
		
	?>
		<p><label for="ilwp-tag-cloud-title">
		<?php _e('Title:') ?> <input type="text" class="widefat" id="ilwp-tag-cloud-title" name="ilwp-tag-cloud-title" value="<?php echo $title ?>" /></label>
		</p>
		<input type="hidden" name="ilwp-tag-cloud-submit" id="ilwp-tag-cloud-submit" value="1" />
	<?php
	}

	function ilwp_widgets_init() {
		if ( !is_blog_installed() )
			return;
		$widget_ops = array('classname' => 'ilwp_widget_tag_cloud', 'description' => __( "A really cool COLORED tag cloud") );
		wp_register_sidebar_widget('ilwp_tag_cloud', __('ILWP Colored Tag Cloud'), 'ilwp_widget_tag_cloud', $widget_ops);
		wp_register_widget_control('ilwp_tag_cloud', __('ILWP Colored Tag Cloud'), 'ilwp_widget_tag_cloud_control' );
		## set default options
		ilwp_set_defaults();
	}


add_action( 'init', 'ilwp_widgets_init', 1 );
add_action( 'admin_menu', 'ctg_options_page' );

?>