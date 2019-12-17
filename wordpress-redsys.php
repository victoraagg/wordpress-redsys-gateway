<?php
/**
 * WordPress Redsys Button
 *
 * Plugin Name: WordPress Redsys Button
 * Version: 1.0.0
 * Author: Víctor Alonso
 * Plugin URI: http://www.victoralonso.me/
 * Description: Botones de pago con RedSys sin necesidad de tener instalado WooCommerce. Configuración muy sencilla.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'REDSYS_WORDPRESS_VERSION', '1.0.0' );
define( 'REDSYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'RedsysAPI' ) ) {
	if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
		require_once 'includes/apiRedsys5.php';
	} else {
		require_once 'includes/apiRedsys7.php';
	}
}

require_once 'includes/cpt-button.php';
require_once 'includes/shortcode-button.php';

function redsys_menu() {
	global $redsys_about;
	$redsys_about = add_menu_page( 'Redsys', 'Redsys', 'manage_options', 'redsys-page', 'redsys_page' );
}
add_action( 'admin_menu', 'redsys_menu' );

function get_info_redsys_response($response) {
	$apiObj = new RedsysAPI;
	$encoded = $apiObj->decodeMerchantParameters($response);
	$decoded = json_decode($encoded);
	$response = $decoded->Ds_Response;
	$order = $decoded->Ds_Order;
	$post_id = substr($order, 6);
	$post = get_post($post_id);
	if($response == '0000' && $post->post_type == 'book'){
		update_post_meta( $post_id, '_book_active', 'Y' );
		echo '<div class="alert dx-alert dx-alert-success">Reserva '.$order.' autorizada</div>';
	}elseif($response == '0000' && $post->post_type == 'inscription'){
		echo '<div class="alert dx-alert dx-alert-success">Inscripción '.$order.' autorizada</div>';
	}else{
		echo '<div class="alert dx-alert dx-alert-danger">Transacción '.$order.' no autorizada</div>';
	}
}
add_action( 'access_api_redsys_public', 'get_info_redsys_response' );

function redsys_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Insufficient permissions'));
    }
    $options = [
		'_redsys_environment' => ['Entorno', 'select', ['TEST','REAL']],
		'_redsys_url_tpv_test' => ['URL tpv - TEST', 'text'],
		'_redsys_url_tpv_prod' => ['URL tpv - REAL', 'text'],
		'_redsys_url_merchant' => ['URL comercio', 'text'],
        '_redsys_fuc_code' => ['Número de comercio - FUC', 'text'],
		'_redsys_terminal' => ['Número de terminal', 'text'],
		'_redsys_keycode_test' => ['Clave secreta de encriptación - TEST', 'text'],
		'_redsys_keycode_prod' => ['Clave secreta de encriptación - REAL', 'text'],
		'_redsys_name' => ['Nombre del Comercio', 'text'],
		'_redsys_currency' => ['Moneda del terminal', 'select', ['978']],
		'_redsys_url_ok' => ['URL OK', 'text'],
		'_redsys_url_ko' => ['URL KO', 'text'],
    ];
    redsys_build_custom_menu_site_options('Redsys options', $options);
}

function redsys_build_custom_menu_site_options($title, $options) {

	$hidden_field_name = 'options_hidden';

    foreach ($options as $key => $option) {
        $opt_val = get_option($key);
        if (!empty($opt_val)) {
            array_push($options[$key], $opt_val);
        } else {
            array_push($options[$key], '');
        }
    }

    if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
        foreach ($_POST as $key => $value) {
            $datatype = substr($key, 0, 8);
            if ($datatype == '_redsys_') {
                update_option($key, $value);
            }
		}
        echo '<div class="updated"><p><strong>Guardado</strong></p></div>';
    } ?>

    <div class="wrap">
		<h1>WordPress Redsys</h1>
		<hr>
        <form name="options" method="post" action="">
			<input type="hidden" name="<?= $hidden_field_name; ?>" value="Y">
			<?php foreach ($options as $key => $option) { ?>
				<label for="<?= $key ?>"><?= $option[0] ?></label>
				<?php 
				if($option[1] == 'select'){
					echo '<select id="'.$key.'" name="'.$key.'">';
					foreach ($option[2] as $option_elect) {
						if($option[3] == $option_elect){
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option '.$selected.' value="'.$option_elect.'">'.$option_elect.'</option>';
					}
					echo '</select>';
				}elseif($option[1] == 'text'){
					echo '<input id="'.$key.'" type="'.$option[1].'" name="'.$key.'" value="'.$option[2].'">';
				}
				?>
                <hr>
            <?php } ?>
			<input type="submit" name="submit" class="button-primary" value="Guardar" />			
		</form>
    </div>
    
    <?php
}
