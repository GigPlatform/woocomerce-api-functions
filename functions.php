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
			  get_orders(data => {
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
				WooCommerce.get('orders', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_customers(callback) {
				WooCommerce.get('customers', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_reports(callback) {
				WooCommerce.get('reports', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			function get_sales(callback) {
				WooCommerce.get('reports/sales', function(err, data, res) {
				    if(err)
						console.log(err);
				    callback(JSON.parse(res));
				});
			}

			<?php
			function get_sales_by_date(date_min, date_max) {
				$query = [
	  			'date_min' => $date_min, 
	    		'date_max' => $date_max
				];
				print_r($woocommerce->get('reports/sales', $query));
				
					/*
					WooCommerce.get('reports/sales', function(err, data, res) {
					    if(err)
							console.log(err);
					    callback(JSON.parse(res));
				});
				*/
			}

			function get_purchased_products(customerID) {
				$query = [
	  			'customer_id' => $customerID
				];
				print_r($woocommerce->get('orders', $query));
				
					/*
					WooCommerce.get('reports/sales', function(err, data, res) {
					    if(err)
							console.log(err);
					    callback(JSON.parse(res));
				});
				*/
			}




	public function filter_orders_report_overview($orders) {
		foreach( $orders as $order_key => $order ) {
			$vendor_item = false;
			$order_obj = new WC_Order( $order->ID );
			$items = $order_obj->get_items( 'line_item' );
			foreach( $items as $item_id => $item ) {
				$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
				$vendor_id = wc_get_order_item_meta( $item_id, '_vendor_id', true );
				$current_user = get_current_vendor_id();
				if( $vendor_id ) {
					if( $vendor_id == $current_user ) {
						$existsids[] = $product_id;
						$vendor_item = true;
					}
				} else {
					//for vendor logged in only
					if ( is_user_wcmp_vendor($current_user) ) {
						$vendor = get_wcmp_vendor($current_user);
						$vendor_products = $vendor->get_products();
						$existsids = array();
						foreach ( $vendor_products as $vendor_product ) {
							$existsids[] = ( $vendor_product->ID );
						}
						if ( in_array( $product_id, $existsids ) ) {
							$vendor_item = true;
						} 
					}
				}
			}
			if(!$vendor_item) unset($orders[$order_key]);
		}
		return $orders;
	}
	
	/**
	 * Show only reports that are useful to a vendor
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function filter_tabs( $tabs ){
		global $woocommerce;
		unset( $tabs[ 'wcmp_vendors' ]['reports']['vendor'] );		
		$return = array(
			'wcmp_vendors' => $tabs[ 'wcmp_vendors' ],
		);
		return $return;
	}

	/** 
	 * WCMp reports tab options
	 */
	function wcmp_report_tabs( $reports ) {
		global $WCMp;		
		$reports['wcmp_vendors'] = array(
			'title'  => __( 'WCMp', 'dc-woocommerce-multi-vendor' ),
			'reports' => array(
				"overview" => array(
					'title'       => __( 'Overview', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"vendor" => array(
					'title'       => __( 'Vendor', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"product" => array(
					'title'       => __( 'Product', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				)
			)
		);
		
		return $reports;
	}
	
	/**
	 * Get a report from our reports subfolder
	 */
	public static function wcmp_get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'WCMp_Report_' . ucfirst( str_replace( '-', '_', $name ) );
		include_once( apply_filters( 'wcmp_admin_reports_path', 'reports/class-wcmp-report-' . $name . '.php', $name, $class ) );
		if ( ! class_exists( $class ) )
			return;
		$report = new $class();
		$report->output_report();
	}


		function create_product(){

		if($_SERVER["REQUEST_METHOD"] == "POST") {
  			if (
		  	isset($_POST['sale_price']) and 
		  	isset($_POST['short_description']) and 
		  	isset($_POST['description']) and 
		  	isset($_POST['catalog_visibility']) and 
		  	isset($_POST['images'])
		    ){
		//
				$sale_price = dashboard($_POST['sale_price']);
				$short_description = dashboard($_POST['short_description']);
				$description = dashboard($_POST['description']);
				$catalog_visibility = dashboard($_POST['catalog_visibility']);
				$images = dashboard($_POST['images']);

				$data=array();

				$data[0]=$sale_price;
				$data[1]=$short_description;
				$data[2]=$description;
				$data[3]=$catalog_visibility;
				$data[4]=$images;

				return $data;
				}
			?>
		</script>
	</body>
</html>
