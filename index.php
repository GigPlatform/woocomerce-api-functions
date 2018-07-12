<html>
	<head>


		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular.min.js"></script>
		<script src="wcapi.js"></script>
	</head>
	<body ng-app='starter.services' ng-controller='myCtrl'>
		<pre id="result" class="prettyprint">Loading ...</pre>

		<script>
			var Woocommerce = null;
			var app = angular.module('starter.services',[]);
			app.service('WC', function(){
			    return {
			        WC: function(){
			            var WoocommerceObj = new WoocommerceAPI({
			                url: 'http://dev.metagig.ml',
			                consumerKey: 'ck_de36cdd05b4708bac395edcf3c6a371c5cb97c4f',
			                consumerSecret: 'cs_b08f9cbe4f9630d232c9056b022bbc795dee8e92',
					        wpAPI: true,
			  		        version: 'wc/v2'
			            })
			            return WoocommerceObj;
			        }
			}});
			app.controller('myCtrl', function($scope, WC){
			  Woocommerce = WC.WC();
			  get_categories(data => {
			  	console.log(data);
			  });
			  get_customers(data => {
			  	console.log(data);
			  });
			  get_orders(data => {
			  	console.log(data);
			  });
			  get_reports(data => {
			  	console.log(data);
			  });
			  get_sales(data => {
			  	console.log(data);
			  });
			  get_sales_by_date(data => {
			  	console.log(data);
			  });

			  get_products(data => {
			  	console.log(data);
			  	document.getElementById('result').innerHTML = 'Loaded'
			  	//JSON.stringify(data, null, 2);
			  });

			});

			function get_categories(callback) {
				Woocommerce.get('products/categories', function(err, data, res){
					if(err)
						console.log(err);
					callback(JSON.parse(res));
				});
			}

			function get_products(callback) {
				Woocommerce.get('products', function(err, data, res){
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_orders(callback) {
				Woocommerce.get('orders', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_customers(callback) {
				Woocommerce.get('customers', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_reports(callback) {
				Woocommerce.get('reports', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}
			function get_sales(callback) {
				Woocommerce.get('reports/sales', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}
			function get_sales_by_date(callback) {
				Woocommerce.get('reports/sales?date_min=2016-05-03&date_max=2016-05-04', 
					function(err, data, res) {
				    	if(err)
							console.log(err);
				    		callback(JSON.parse(res));
					});
			}	
		</script>


	<?php

	require __DIR__ .'/vendor/autoload.php';
	use Automattic\WooCommerce\Client;
	$woocommerce = new Client(
   		'http://dev.metagig.ml', 
    	'ck_de36cdd05b4708bac395edcf3c6a371c5cb97c4f', 
    	'cs_b08f9cbe4f9630d232c9056b022bbc795dee8e92',
   	 	[
        	'wp_api' => true,
        	'version' => 'wc/v2',
    	]
	);

	function create_product(){
		global $woocommerce;
		$data = [
		    'name' => 'Premium Quality',
		    'sale_price' => '21.99',
		    'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
		    'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
		    		    'images' => [
		        [
		            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg',
		            'position' => 0
		        ],
		        [
		            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg',
		            'position' => 1
		        ]
		    ],
		    'categories' => [
		        [
		            'id' => 9
		        ],
		        [
		            'id' => 14
		        ]
		    ]

		];

		print_r($woocommerce->post('products', $data));
		
		//echo "<script>console.log(".json_encode($data).")</script>";
	}

	function add_vendor(){
		global $woocommerce;
	$data = [
	    'first_name' => 'John',
	    'last_name' => 'Doe',
	    'profile_picture' => [
	        [
	            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg',
	            'position' => 0
	        ]
		],
	    'description' => 'description',
	    'billing' => [
	        'city' => 'San Francisco',
	        'state' => 'CA',
	        'postcode' => '94103',
	        'country' => 'US',
	        'phone' => '(555) 555-5555',
	        'payment_mode' => 'PayPal',
	        'PayPal_email' => 'test@mail.com',
    ]
];

		print_r($woocommerce->post('vendors', $data));
		//echo "<script>console.log(".json_encode($data).")</script>";
	}

	function store_front(){
		global $woocommerce;
	$data = [
	    
	    'profile_picture' => [
	        [
	            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg',
	            'position' => 0
	        ]
		],
		'store_name' => 'Test',
	    'store_description' => 'description',
	    'message_to_buyers' => 'message',
	    'store_adress' => [
	        'city' => 'San Francisco',
	        'state' => 'CA',
			'country' => 'US',
	        'postcode' => '94103',
	        'currency' => 'USD',
	        'store_location' => 'location'
    ]
];

		print_r($woocommerce->post('customers', $data));

		
		//echo "<script>console.log(".json_encode($data).")</script>";
	}

?>
		 <?php
			  
			  create_product();
			  store_front();
			  add_vendor();
			  ?>

	</body>
</html>
