<?php
function add_vendor_custom_field($user) {
    // Check if user type is vendor
    if (in_array('yith_vendor', (array) $user->roles)) {
        ?>
        <h3><?php esc_html_e('Vendor DHL Details', 'text-domain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Account Number', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_account_number" id="vendor_account_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_account_number', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor account number.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Api Key', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_api_key" id="vendor_api_key" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_api_key', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor api Key.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Api Secret', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_api_secret" id="vendor_api_secret" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_api_secret', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor api secret key.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Postal Code', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_postal_code" id="vendor_postal_code" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_postal_code', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor postal code.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor City', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_city" id="vendor_city" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_city', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor City.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Country Code', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_country_code" id="vendor_country_code" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_country_code', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor Country Code.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Province Code', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_provinceCode" id="vendor_provinceCode" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_provinceCode', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor Province Code.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Address', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_address" id="vendor_address" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_address', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor address.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor County Name', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_countyName" id="vendor_countyName" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_countyName', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor County Name.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Province Name', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_provinceName" id="vendor_provinceName" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_provinceName', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor Province Name.', 'text-domain'); ?></p>
                </td>
            </tr>
			<tr>
                <th><label for="vendor_custom_field"><?php esc_html_e('Vendor Country Name', 'text-domain'); ?></label></th>
                <td>
                    <input type="text" name="vendor_countryName" id="vendor_countryName" value="<?php echo esc_attr(get_user_meta($user->ID, 'vendor_countryName', true)); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter vendor Country Name.', 'text-domain'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
}
add_action('show_user_profile', 'add_vendor_custom_field');
add_action('edit_user_profile', 'add_vendor_custom_field');

// Save custom field data
function save_vendor_custom_field($user_id) {
    // Check if user type is vendor
    if (in_array('yith_vendor', (array) get_userdata($user_id)->roles)) {
        if (current_user_can('edit_user', $user_id)) {
            update_user_meta($user_id, 'vendor_account_number', sanitize_text_field($_POST['vendor_account_number']));
			update_user_meta($user_id, 'vendor_api_key', sanitize_text_field($_POST['vendor_api_key']));
			update_user_meta($user_id, 'vendor_api_secret', sanitize_text_field($_POST['vendor_api_secret']));
			update_user_meta($user_id, 'vendor_postal_code', sanitize_text_field($_POST['vendor_postal_code']));
			update_user_meta($user_id, 'vendor_city', sanitize_text_field($_POST['vendor_city']));
			update_user_meta($user_id, 'vendor_country_code', sanitize_text_field($_POST['vendor_country_code']));
			update_user_meta($user_id, 'vendor_provinceCode', sanitize_text_field($_POST['vendor_provinceCode']));
			update_user_meta($user_id, 'vendor_address', sanitize_text_field($_POST['vendor_address']));
			update_user_meta($user_id, 'vendor_countyName', sanitize_text_field($_POST['vendor_countyName']));
			update_user_meta($user_id, 'vendor_provinceName', sanitize_text_field($_POST['vendor_provinceName']));
			update_user_meta($user_id, 'vendor_countryName', sanitize_text_field($_POST['vendor_countryName']));
			//update_user_meta($user_id, 'vendor_provinceName', sanitize_text_field($_POST['vendor_provinceName']));
        }
    }
}
add_action('personal_options_update', 'save_vendor_custom_field');
add_action('edit_user_profile_update', 'save_vendor_custom_field');

/*add_filter('woocommerce_short_description', 'add_custom_text_to_short_description');

function add_custom_text_to_short_description($short_description) {
    
	$productid=get_the_ID();
	$vendor_id = get_post_field( 'post_author', $product_id );
	$vendor = get_userdata( $vendor_id );
	$email = $vendor->user_email;
    $custom_text = 'This is the custom text you want to add.';
    $short_description .= '<p>' . $email . '</p>';

    return $short_description;
}*/
add_filter('woocommerce_add_to_cart_validation', 'check_vendor_before_add_to_cart', 10, 3);

function check_vendor_before_add_to_cart($passed, $product_id, $quantity) 
{
    $vendor_id = get_post_field( 'post_author', $product_id );
	$cart = WC()->cart->get_cart();
	foreach ($cart as $cart_item_key => $cart_item) {
        $cart_product_id = $cart_item['product_id'];
        $cart_product_vendor = get_post_field( 'post_author', $cart_product_id );
		if ($cart_product_vendor !== $vendor_id) {
            wc_add_notice(__('You can only add products from the same vendor to the cart. Or first remove previously added product from cart. because that product is from differrent vendor to the current product', 'your-text-domain'), 'error');
            $passed = false; 
            break;
        }else{
			$passed = true;
		}
    }
	
    return $passed;
}