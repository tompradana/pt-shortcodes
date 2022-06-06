<?php
/**
 * Plugin Name: Post Type Shortcodes
 * Author: Tommy Pradana
 * Author URI: https://www.sribulancer.com/id/users/tompradana
 * Version: 1.0.0
 * Text Domain: textdomain
 * Description: PT Shorcodes plugin
 * 
 * You should have received a copy of the GNU General Public License. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! function_exists( 'add_action' ) ) exit;

define( 'PTS_PLUGIN_NAME',      'Post Type Shortcodes'      );
define( 'PTS_POST_TYPE_SLUG',   'guest'                     );

/**
 * The Shortcode :)
 *
 * @param [type] $atts
 * @return void
 */
function pts_plugin_shortcode_guestnum( $atts ) {
    $args = shortcode_atts( [
        'clientid'  => '',
        'guestid'   => '',
        'name'      => '',
        'phone'     => '',
        'rsvp'      => '',
        'rsvp_date' => '',
        'table'     => '',
        'remark'    => '',
        'sesi'      => '',
        'status'    => ''
    ], $atts, 'pts_guestnum' );

    $guestnum   = 0;
    $meta_query = [];

    // build meta query
    // this method is slower then $wpdb->get_rows()
    foreach( $args as $meta_key => $value ) {
        if ( $value <> "" ) {
            $meta_query[] = [
                'key'   => $meta_key,
                'value' => esc_attr( $value )
            ];
        }
    }

    if ( !empty( $meta_query ) ) {
        if ( count( $meta_query ) > 1 ) {
            $relation = [ 'relation' => 'AND' ];
            $meta_query = array_merge( $relation, $meta_query );
        }

        // create the query :P
        $custom_query = new WP_Query( [
            'post_type'         => PTS_POST_TYPE_SLUG,
            'post_status'       => 'publish',
            'posts_per_page'    => -1, // get all posts
            'meta_query'        => $meta_query
        ] );
        
        // We can use while andwhile; but i want to use foreach loop here, to get the data faster
        if ( $custom_query->posts ) {
            foreach( $custom_query->posts as $k => $the_post ) {
                $guestnum += (int) get_post_meta( $the_post->ID, 'guestnum', true );
            }
        }
    }

    // Use return instead of echo
    // Otherwise $guestnum always 0
    return $guestnum;
}
add_shortcode( 'pts_get_guestnum', 'pts_plugin_shortcode_guestnum' );

/**
 * Ehem
 *
 * @param [type] $plugin_meta
 * @param [type] $plugin_file
 * @param [type] $plugin_data
 * @param [type] $status
 * @return void
 */
function pts_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
    if ( $plugin_data['Name'] == PTS_PLUGIN_NAME ) {
        $plugin_meta[] = sprintf(
            '%1$s: <a href="mailto:%2$s" aria-label="%1$s">%2$s</a>',
            __( 'Support', 'textdomain' ),
            'tom.wpdev@gmail.com'
        );

        $plugin_meta[] = sprintf(
            '%1$s: <a target="_blank" href="https://wa.me/6282331696659" aria-label="%1$s">62 823-3169-6659</a>',
            __( 'WhatsApp', 'textdomain' )
        );
    }
    return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'pts_plugin_row_meta', 10, 4 );

/**
 * Sample data
 *
 * @return void
 */
function pts_plugin_insert_sample_data() {
    // delete_option( 'pt_plugin_sample_data' );

    if ( 'done' != get_option( 'pt_plugin_sample_data' ) ) { 
        update_option( 'pt_plugin_sample_data', 'done' );
        $data = [
            ["wed123456", "guest111223", "Thomas", 8515696977, "Yes", "14-05-2022", 20, "", "", "Reception", "No-show"],
            ["wed123456", "guest111224", "Lovisa", 8515696978, "Yes", "14-05-2022", 2, "", "", "Reception", "No-show"],
            ["wed123456", "guest111225", "John", 8515696979, "Yes", "14-05-2022", 4, "", "", "Ceremony", "Check-in"],
            ["wed123456", "guest111226", "Leno", 8515696980, "No", "14-05-2022", 0, "", "", "Ceremony", "No-show"],
            ["wed123458", "guest111227", "Mike", 8515696981, "Yes", "30-04-2022", 5, "", "", "Ceremony", "Check-in"],
            ["wed123458", "guest111228", "Dessy", 8515696982, "Yes", "30-04-2022", 2, "", "", "Ceremony", "Check-in"], 
            ["wed123459", "guest111229", "Dilla", 8515696983, "Yes", "04-04-2022", 1, "", "", "Ceremony", "Check-in"],
            ["wed123459", "guest111230", "Yogi", 8515696984, "Yes", "04-04-2022", 1, "", "", "Ceremony", "Check-in"], 
            ["wed123459", "guest111231", "Julia", 8515696984, "Yes", "04-05-2022", 1, "", "", "Ceremony", "Check-in"], 
            ["wed123459", "guest111232", "Regy", 8515696984, "Yes", "04-06-2022", 2, "", "", "Ceremony", "Check-out"], 
            ["wed123459", "guest111233", "Deno", 8515696984, "Yes", "04-07-2022", 3, "", "", "Ceremony", "Check-out"]
        ];

        foreach( $data as $k => $v ) {
            $post_id = wp_insert_post(
                [
                    'post_type'     => 'guest',
                    'post_title'    => $v[1], // guestid
                    'post_status'   => 'publish'
                ],
                true
            );
            if ( !is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'clientid',     $v[0] );
                update_post_meta( $post_id, 'guestid',      $v[1] );
                update_post_meta( $post_id, 'your_name',    $v[2] );
                update_post_meta( $post_id, 'phone',        $v[3] );
                update_post_meta( $post_id, 'rsvp',         $v[4] );
                update_post_meta( $post_id, 'rsvp_date',    $v[5] );
                update_post_meta( $post_id, 'guestnum',     $v[6] );
                update_post_meta( $post_id, 'table',        $v[7] );
                update_post_meta( $post_id, 'remark',       $v[8] );
                update_post_meta( $post_id, 'sesi',         $v[9] );
                update_post_meta( $post_id, 'status',       $v[10] );
            }
        }
    }
}
// add_action( 'admin_init', 'pts_plugin_insert_sample_data' );