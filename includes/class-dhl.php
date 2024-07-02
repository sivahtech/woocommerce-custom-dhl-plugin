<?php
class DHL
{
    private  $apiKey = "apS7tI3zE6lV3a";
    private  $apiSecret = "U$6oW!1dG$4sR@4h";
    private  $BASE_URL = 'https://express.api.dhl.com/mydhlapi/test/';
    private  $auth = '';
    private  $accountNo = '470580929';

    function __construct()
    {
        $this->BASE_URL = 'https://express.api.dhl.com/mydhlapi/test/';
        $this->auth = base64_encode($this->apiKey . ":" . $this->apiSecret);
    }

    public function curlGetRequest($endpoint, $queryParams)
    {
        // Initialize cURL session
        $ch = curl_init();

        // Build query string
        $query = http_build_query($queryParams);

        // Construct the complete URL
        $url = $this->BASE_URL . $endpoint . '?' . $query;

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $this->auth,
            'Content-type: Application-json'
        ]);


        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors
        $err = curl_error($ch);
        if ($err) {
            $response = "cURL Error #:" . $err;
        }

        // Close cURL session
        curl_close($ch);

        return $response;
    }

    public function curlPostRequest($endpoint, $request_body, $queryParams = [])
    {
		$query = http_build_query($queryParams);
		$url = $this->BASE_URL . $endpoint;
		$requestJson = json_encode($request_body);
		$curl = curl_init();
		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($request_body),
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . $this->auth,
                'Content-type: Application-json'
            ],
        ));

        $response = curl_exec($curl);
		curl_close($curl);
        echo $response;
    }


    public function validatePickupAddress()
    {
        $endpoint = 'address-validate';
        $queryParams = [
            'type' => 'pickup',
            'countryCode' => 'IN',
            'postalCode' => '160055',
            'cityName' => 'mohali'
        ];

        $response = $this->curlGetRequest($endpoint, $queryParams);
        return $response;
    }

    public function validateDeliveryAddress()
    {

        $endpoint = 'address-validate';
        $queryParams = [
            'type' => 'delivery',
            'countryCode' => $_POST['country_code'],
            'postalCode' => $_POST['postal_code'],
            'cityName' => $_POST['city'],
        ];

        $response = $this->curlGetRequest($endpoint, $queryParams);
        return json_decode($response, true);
    }

    public function createShipment($ship=array(),$bill=array(),$orderitems=array(),$shipcode="",$estimated_delivery_date="",$ncode="",$total_weight = "",$total_length="",$total_width="",$total_height="",$cart_product_vendor='')
    {
		/*echo "<pre>";
			print_r($ship);
		echo "</pre>";
		echo "<pre>";
			print_r($bill);
		echo "</pre>";
		echo "<pre>";
			print_r($orderitems);
		echo "</pre>";
		echo $shipcode;
		echo $ncode;
		die();
		*/
		if($ship['company'] !=''){
			$company=$ship['company'];
		}elseif($bill['company'] !=''){
			$company=$bill['company'];
		}else{
			$company=$ship['first_name'] . ' ' . $ship['last_name'];
		}
        //$currentDate = new DateTime($estimated_delivery_date);
		//$formattedDate = $currentDate->format('Y-m-d\TH:i:s') . " GMT+00:00";
		//$formattedDate = $estimated_delivery_date;
		$input_date = new DateTime($estimated_delivery_date);
		$date = $input_date->format('Y-m-d');
		$time = $input_date->format('H:i:s');
		$output_date_string = $date . 'T' . $time . 'GMT+01:00';
		if($cart_product_vendor !=''){
			$user_info = get_userdata($cart_product_vendor);
			$postalcode=!empty(get_user_meta($cart_product_vendor, 'vendor_postal_code', true)) ? get_user_meta($cart_product_vendor, 'vendor_postal_code', true) : '2413';
			$cityName=!empty(get_user_meta($cart_product_vendor, 'vendor_city', true)) ? get_user_meta($cart_product_vendor, 'vendor_city', true) : 'Nicosia';
			$countryCode=!empty(get_user_meta($cart_product_vendor, 'vendor_country_code', true)) ? get_user_meta($cart_product_vendor, 'vendor_country_code', true) : 'cy';
			$provinceCode=!empty(get_user_meta($cart_product_vendor, 'vendor_provinceCode', true)) ? get_user_meta($cart_product_vendor, 'vendor_provinceCode', true) : 'Egkomi';
			$addressLine1=!empty(get_user_meta($cart_product_vendor, 'vendor_address', true)) ? get_user_meta($cart_product_vendor, 'vendor_address', true) : 'Odyssea antroutsou 3';
			$countyName=!empty(get_user_meta($cart_product_vendor, 'vendor_countyName', true)) ? get_user_meta($cart_product_vendor, 'vendor_countyName', true) : 'Cyprus';
			$provinceName=!empty(get_user_meta($cart_product_vendor, 'vendor_provinceName', true)) ? get_user_meta($cart_product_vendor, 'vendor_provinceName', true) : 'Egkomi';
			$countryName=!empty(get_user_meta($cart_product_vendor, 'vendor_countryName', true)) ? get_user_meta($cart_product_vendor, 'vendor_countryName', true) : 'Cyprus';
			$billingemail=!empty($user_info->user_email) ? $user_info->user_email : 'admin@connectart.io';
			$fullName=!empty($user_info->display_name) ? $user_info->display_name : 'Anastasios Aristidou';
			$phone=!empty(get_user_meta($cart_product_vendor, 'billing_phone', true)) ? get_user_meta($cart_product_vendor, 'billing_phone', true) : '+35799214164';
			$mobilrphone=!empty(get_user_meta($cart_product_vendor, 'billing_mobile', true)) ? get_user_meta($cart_product_vendor, 'billing_mobile', true) : '+35799214164';
			
			$company=!empty(get_user_meta($cart_product_vendor, 'billing_company', true)) ? get_user_meta($cart_product_vendor, 'billing_company', true) : 'ConnectArt S.T Limited';
			
		}else{
			$postalcode='2413';
			$cityName='Nicosia';
			$countryCode='cy';
			$provinceCode='Egkomi';
			$addressLine1='Odyssea antroutsou 3';
			$countyName='Cyprus';
			$provinceName='Egkomi';
			$countryName='Cyprus';
			$phone='+35799214164';
			$mobilrphone='+35799214164';
			$company='ConnectArt S.T Limited';
			$billingemail='admin@connectart.io';
			$fullName='Anastasios Aristidou';
		}
        $endpoint = 'shipments';
        $data = [
            'plannedShippingDateAndTime' => $output_date_string,
            "pickup" => [
                "isRequested" => false,
                "closeTime" => "18:00",
                "location" => "reception",
                "specialInstructions" => [
                    [
                        "value" => "please ring door bell",
                        "typeCode" => $ncode
                    ]
                ]
            ],
            'productCode' => $shipcode,
            'getRateEstimates' => true,
            'accounts' => [
                [
                    'typeCode' => 'shipper',
                    'number' => $this->accountNo
                ]
            ],
            'customerDetails' => [
                'shipperDetails' => [ // vendor details 
                    'postalAddress' => [
                        'postalCode' => $postalcode,
                        'cityName' => $cityName,
                        'countryCode' => $countryCode,
                      //  'provinceCode' => $provinceCode,
                        'addressLine1' => $addressLine1,
                       // 'countyName' => $countyName,
                        'provinceName' => $provinceName,
                        'countryName' => $countryName
                    ],
                    'contactInformation' => [
                        'email' => $billingemail,
                        'phone' => $phone,
                        'mobilePhone' => $mobilrphone,
                        'companyName' => $company,
                        'fullName' => $fullName
                    ],
                    'typeCode' => 'business'
                ],
                'receiverDetails' => [
                    'postalAddress' => [
                        'postalCode' => $ship['postcode'],
                        'cityName' => $ship['city'],
                        'countryCode' => $ship['country'],
                        'provinceCode' => $ship['state'],
                        'addressLine1' => $ship['address_1'],
                        'countyName' => $ship['state'],
                        'provinceName' => $ship['country'],
                        'countryName' => $ship['country']
                    ],
                    'contactInformation' => [
                        'email' => $bill['email'],
                        'phone' => $bill['phone'],
                        'mobilePhone' => $bill['phone'],
                        'companyName' => $company,
                        'fullName' => $ship['first_name'] . ' ' . $ship['last_name']
                    ],
                    'typeCode' => 'business'
                ]

            ],
            'content' => [
                'packages' => [   // product details 
                    [
                      //  'typeCode' => '2BP',
                        'weight' => $total_weight,
                        'dimensions' => [
                            'length' => $total_length,
                            'width' => $total_width,
                            'height' => $total_height
                        ],
                    ]
                ],
                'isCustomsDeclarable' => false,
                
                'description' => 'shipment description',
              
                'incoterm' => 'DAP',
                'unitOfMeasurement' => 'metric'
            ]
        ];

		//echo "<pre>";
		//print_r($data);
		///echo "</pre>";
		//die();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->BASE_URL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . $this->auth,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }



    public function availableProducts()
    {
        $queryParams = [
            'accountNumber' => '470580929',
            'originCountryCode' => 'CY',
            'originCityName' => 'Tseri',
            'destinationCountryCode' => 'IN',
            'destinationCityName' => 'Mohali',
            'weight' => 5,
            'length' => 15,
            'width' => 10,
            'height' => 5,
            'plannedShippingDate' => '2024-04-26',
            'isCustomsDeclarable' => 'false',
            'unitOfMeasurement' => 'metric',

        ];

        $endpoint = 'products';

        $response = $this->curlGetRequest($endpoint, $queryParams);
        return $response;
    }
	public function trackorder($trackingnumber='')
	{
		$queryParams = [
            'shipmentTrackingNumber' => $trackingnumber
        ];
		$endpoint = 'shipments/'.$trackingnumber.'/tracking';
		$response = $this->curlGetRequest($endpoint, $queryParams);
		
        return $response;
	}
    public function rating($destinationcn="",$destinationcity="",$destpost='',$weight='',$length='',$width='',$height='',$cart_product_vendor='')
    {
        //$productId = $_POST['productId'];
        if($weight==''){
			$weight=5;
		}
		if($length==''){
			$length=15;
		}
		if($width==''){
			$width=10;
		}
		if($height==''){
			$height=5;
		}
		//$date=
		$currentDate = new DateTime();
		$currentDate->modify('+2 days');
		$newDate = $currentDate->format('Y-m-d');
		if($cart_product_vendor !=''){
			$vendor_cn_code=!empty(get_user_meta($cart_product_vendor, 'vendor_country_code', true)) ? get_user_meta($cart_product_vendor, 'vendor_country_code', true) : 'cy';
			$vendor_city_code=!empty(get_user_meta($cart_product_vendor, 'vendor_city', true)) ? get_user_meta($cart_product_vendor, 'vendor_city', true) : 'Nicosia';
		}else{
			$vendor_cn_code='cy';
			$vendor_city_code='Nicosia';
		}
		
        $queryParams = [
            'accountNumber' => $this->accountNo,
            'originCountryCode' => $vendor_cn_code,  // vendor wareHouse country code
            'originCityName' => $vendor_city_code,   // vendor warehouse ciy 
            'destinationCountryCode' => $destinationcn,
            'destinationCityName' => $destinationcity,
			'destinationPostalCode'=>$destpost,
            'weight' => $weight,   // products details 
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'plannedShippingDate' => $newDate,
            'isCustomsDeclarable' => 'false',
            'unitOfMeasurement' => 'metric',
        ];
		
        // Set base URL
        $endpoint = 'rates';

        $response = $this->curlGetRequest($endpoint, $queryParams);
        return json_decode($response, true);
    }
}