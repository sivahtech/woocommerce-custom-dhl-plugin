<?php
/**
 * Custom DHL Express Shipping Method
 */

class Custom_DHL_Express_Shipping_Method extends WC_Shipping_Method {
    public function __construct( $instance_id = 0 ) {
        $this->id                 = 'custom_dhl_express_shipping';
		$this->instance_id        = absint( $instance_id );
        $this->method_title       = __('DHL Express');
        $this->method_description = __('Custom DHL Express shipping method for WooCommerce');

        $this->title              = __( 'DHL Express', 'text-domain' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		// then you have to call this method to initiate your settings
		$this->init();
    }

    public function init() {
        $this->init_form_fields();
		// this is settings instance where you can declar your settings field 
		$this->init_instance_settings();
		
		// user defined values goes here, not in construct 
		$this->enabled = $this->get_option( 'enabled' );
		$this->title   = __( 'DHL Express', 'text-domain' );
		
		// call this action in init() method to save your settings at the backend
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }
	public function init_instance_settings() {
	// you have to keep all the instance settings field inside the init_instance_settings method 
	$this->instance_form_fields = array(
		'enabled'    => array(
			'title'   => __( 'Enable/Disable' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable this shipping method' ),
			'default' => 'yes',
		),
		'title'      => array(
			'title'       => __( 'Method Title' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.' ),
			'default'     => __( 'Pickup Location' ),
			'desc_tip'    => true
		),
		'tax_status' => array(
			'title'   => __( 'Tax status', 'woocommerce' ),
			'type'    => 'select',
			'class'   => 'wc-enhanced-select',
			'default' => 'taxable',
			'options' => array(
				'taxable' => __( 'Taxable', 'woocommerce' ),
				'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
			),
		),
		'cost'       => array(
			'title'       => __( 'Cost', 'woocommerce' ),
			'type'        => 'text',
			'placeholder' => '0',
			'description' => __( 'Optional cost for pickup.', 'woocommerce' ),
			'default'     => '',
			'desc_tip'    => true,
		),
	);
}
    public function calculate_shipping($package = array()) 
	{
		
		
		$cart = WC()->cart;
		$total_length = 0;
		$total_width = 0;
		$total_height = 0;
		$cart_product_vendor='';
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$product_id = $cart_item['product_id'];
			$cart_product_vendor = get_post_field( 'post_author', $product_id );
			$product = wc_get_product($product_id);
			
			 if ( $product->get_length()){
				 $total_length +=$product->get_length();
			 }
			 if ( $product->get_width()){
				 $total_width +=$product->get_width();
			 }
			 if ( $product->get_height()){
				 $total_height +=$product->get_height();
			 }
		}
		
		$customer_country = WC()->customer->get_shipping_country();
		$customer_state = WC()->customer->get_shipping_state();
		$customer_postcode = WC()->customer->get_shipping_postcode();
		$customer_city = WC()->customer->get_shipping_city();
		$weight = WC()->cart->get_cart_contents_weight();
		$DHL = new DHL();
		$rates = $DHL->rating($customer_country,$customer_city,$customer_postcode,$weight,$total_length,$total_width,$total_height,$cart_product_vendor);
		//var_dump($rates);
		//echo "<pre>";
		//print_r($rates['products']);
		
		//echo "</pre>";
		foreach ($rates['products'] as $rate) {
			
			
			$this->add_rate(array(
				'id' => $this->id,
				'label' => "DHL Express(".$rate['productCode']."):".$rate['productName']."( Estimated Delivery Date: ".$rate['deliveryCapabilities']['estimatedDeliveryDateAndTime'].")(NCode:".$rate['networkTypeCode'].")",
				'cost' => $rate['totalPrice']['0']['price'],
				'taxes'   => false,
				'package' => $package,
				'shipping_date' => $rate['deliveryCapabilities']['estimatedDeliveryDateAndTime'],
			));
		}
    }
}

// Register the custom DHL Express shipping method
function add_custom_dhl_express_shipping_method($methods) {
	
    $methods['custom_dhl_express_shipping'] = 'Custom_DHL_Express_Shipping_Method';
	
    return $methods;
}
add_filter('woocommerce_shipping_methods', 'add_custom_dhl_express_shipping_method');

// Enqueue JavaScript for updating shipping rates dynamically with AJAX
function enqueue_custom_dhl_express_shipping_ajax() {
    wp_enqueue_script('custom-dhl-express-shipping-ajax', plugin_dir_url(__FILE__) . 'js/custom-dhl-express-shipping-ajax.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-dhl-express-shipping-ajax', 'custom_dhl_express_shipping_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('custom_dhl_express_shipping_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_dhl_express_shipping_ajax');

// Handle AJAX request to update shipping rates dynamically
add_action('wp_ajax_update_custom_dhl_express_shipping_rates', 'update_custom_dhl_express_shipping_rates');
add_action('wp_ajax_nopriv_update_custom_dhl_express_shipping_rates', 'update_custom_dhl_express_shipping_rates');
function update_custom_dhl_express_shipping_rates() {
    if (isset($_POST['country']) && isset($_POST['state']) && isset($_POST['postcode']) && isset($_POST['city'])) {
        WC()->cart->calculate_shipping();
        WC()->cart->calculate_totals();
    }
    wp_die();
}
add_action('woocommerce_checkout_order_processed', 'create_dhl_shipment');
function create_dhl_shipment($order_id) {
    $order = wc_get_order($order_id);
	$chosen_shipping_method_id = $order->get_shipping_method();
	if (strpos($chosen_shipping_method_id, 'DHL Express') !== false) 
	{ 
		preg_match('/Estimated Delivery Date: ([^\)]+)/', $chosen_shipping_method_id, $matches2);
		$estimated_delivery_date = isset($matches2[1]) ? $matches2[1] : '';
		preg_match('/\(NCode:(.*?)\)/', $chosen_shipping_method_id, $matches3);
		$ncode = isset($matches3[1]) ? $matches3[1] : '';
		$smethod=explode(':',$chosen_shipping_method_id);
		$productcodearray=$smethod['0'];
		preg_match('/\((.*?)\)/', $productcodearray, $matches);
		$bracket_text = isset($matches[1]) ? $matches[1] : '';
		$shippingpcode= $bracket_text;
		
		$shipping_address = $order->get_address('shipping'); 
		$billing_address = $order->get_address('billing'); 
		$total_weight = 0;
		$total_length = 0;
		$total_width = 0;
		$total_height = 0;
		$cart_product_vendor='';
		foreach( $order->get_items() as $item_id => $product_item ){
			$product_id = $item_id;
			$cart_product_vendor = get_post_field( 'post_author', $product_id );
			$quantity = $product_item->get_quantity();
			$product = $product_item->get_product();
			$product_weight = $product->get_weight();
			if($product_weight){
				$total_weight += floatval( $product_weight * $quantity );
			}
			if ( $product->get_length()){
				 $total_length +=$product->get_length();
			 }
			 if ( $product->get_width()){
				 $total_width +=$product->get_width();
			 }
			 if ( $product->get_height()){
				 $total_height +=$product->get_height();
			 }
		}
		
		
		$DHL = new DHL();
		$dhl_api_response = $DHL->createShipment($shipping_address, $billing_address, $order_items,$shippingpcode,$estimated_delivery_date,$ncode,$total_weight,$total_length,$total_width,$total_height,$cart_product_vendor );
		
		$shipment = json_decode($dhl_api_response, true);
		
        if (isset($shipment['status']) && $shipment['status'] != 200) {
            //$order->update_status('shipped', __('Failed to create DHL shipment for order: ', 'your-text-domain') . $dhl_api_response['tracking_number']);
			$order->update_status('failed', __('Failed to create DHL shipment for order: ', 'your-text-domain') . $shipment['shipmentTrackingNumber']);
        } else {
            $data = [
                'shipmentTrackingNumber' => $shipment['shipmentTrackingNumber'],
                'trackingUrl' => $shipment['trackingUrl']
            ];
			$msg='Order shipped via DHL. Your Shipment Tracking Number is '.$shipment['shipmentTrackingNumber'].' and package tracking number is '.$shipment['packages']['0']['trackingNumber'].' and tracking url is '.$shipment['packages']['0']['trackingUrl'].'';
			$note_id = wc_create_order_note( $order_id, $msg );
			$order->update_meta_data( 'shipment_tracking_number', $shipment['shipmentTrackingNumber'] );
			
			$order->update_status('shipped', __('Order shipped via DHL. Tracking information: ', 'your-text-domain') . $shipment['shipmentTrackingNumber']);
			$order->save();
        }

	}
}
add_shortcode('shortcode_to_show_order_tracking','custom_function_to_track_dhl_order');
function custom_function_to_track_dhl_order()
{
	ob_start();
	if(isset($_POST['track_order'])){
		$trackingno=$_POST['tracking_number'];
		$DHL = new DHL();
		$dhl_api_response = $DHL->trackorder($trackingno);
		$trackresponse = json_decode($dhl_api_response, true);
		//echo "<pre>";
		//	print_r($trackresponse);
		//echo "</pre>";
	}
	?>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
 <style>
        .container {
            margin-top: 50px;
        }
        .card {
            width: 100%;
            margin: 0 auto;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }
        .card-title {
            color: #007bff;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .card-body p {
            margin-bottom: 5px;
        }
    </style>
<div class="container">
    <form action="" method="post">
        <div class="form-group">
            <label for="tracking_number">Shipment Tracking Number:</label>
            <input type="text" class="form-control" id="tracking_number" name="tracking_number" required>
        </div>
        <div class="form-group">
            <label for="tracking_number">Order ID:</label>
            <input type="text" class="form-control" id="tracking_number_order" name="tracking_number_order" >
        </div>
       
        
        <input name="track_order" value="Submit" type="submit" class="btn btn-primary"/>
    </form>
</div>
<?php if(isset($trackresponse['shipments'])){ ?>
<div class="container">
    <h2>Tracking Information</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Shipment Details</h5>
            <?php
            foreach ($trackresponse['shipments'] as $shipment) {
                ?>
                <p><strong>Tracking Number:</strong> <?php echo $shipment['shipmentTrackingNumber']; ?></p>
                <p><strong>Status:</strong> <?php echo $shipment['status']; ?></p>
                <p><strong>Shipment Description:</strong> <?php echo $shipment['description']; ?></p>
                <p><strong>Shipment Timestamp:</strong> <?php echo $shipment['shipmentTimestamp']; ?></p>
                <p><strong>Total Weight:</strong> <?php echo $shipment['totalWeight']; ?></p>
                <p><strong>Unit of Measurement:</strong> <?php echo $shipment['unitOfMeasurements']; ?></p>
                
                <h5 class="card-title">Shipper Details</h5>
                <p><strong>Name:</strong> <?php echo $shipment['shipperDetails']['name']; ?></p>
                <p><strong>City:</strong> <?php echo $shipment['shipperDetails']['postalAddress']['cityName']; ?></p>
                <p><strong>County:</strong> <?php echo $shipment['shipperDetails']['postalAddress']['countyName']; ?></p>
                <p><strong>Postal Code:</strong> <?php echo $shipment['shipperDetails']['postalAddress']['postalCode']; ?></p>
                <p><strong>Country:</strong> <?php echo $shipment['shipperDetails']['postalAddress']['countryCode']; ?></p>
                
                <h5 class="card-title">Receiver Details</h5>
                <p><strong>Name:</strong> <?php echo $shipment['receiverDetails']['name']; ?></p>
                <p><strong>City:</strong> <?php echo $shipment['receiverDetails']['postalAddress']['cityName']; ?></p>
                <p><strong>County:</strong> <?php echo $shipment['receiverDetails']['postalAddress']['countyName']; ?></p>
                <p><strong>Postal Code:</strong> <?php echo $shipment['receiverDetails']['postalAddress']['postalCode']; ?></p>
                <p><strong>Country:</strong> <?php echo $shipment['receiverDetails']['postalAddress']['countryCode']; ?></p>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php } ?>
	
	<?php
	return ob_get_clean();
}