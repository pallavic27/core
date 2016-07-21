<?php 
if(!defined('ABSPATH')) {exit;}

if(!class_exists('CC_Updater')) :

	class CC_Updater {
		
	public $auth_url = 'http://club.wpeka.com/club_api/';
	public $username;
	public $password;
		
	public function __construct($username, $password) {
		
		$this->username = $username;
		$this->password = $password;
	}
	
	public function validate() {
		
		$strResponseMessage = '';
		$username = $this->username;	$password = $this->password;
		$current_theme_name = apply_filters( 'cyberchimps_current_theme_name', 'CyberChimps' );
		$api_values = array(
				'cc_action_to_take' => 'check_cc_login_details',
				'username'	   => $username,
				'password'	   => $password,
				'theme_name'   => $current_theme_name,
		);
		$options = array(
				'timeout' => 30,
				'body'			=> $api_values
		);
			
		$url = 'https://cyberchimps.com/verify_user.php';
		//$url = 'http://localhost/wordpress_4.1.1/verify_user.php';
		$responseFromvalidateUser = wp_remote_post($url, $options);
		
		if ( ! is_wp_error( $responseFromvalidateUser ) && wp_remote_retrieve_response_code( $responseFromvalidateUser ) == 200 ){
		
			$response = wp_remote_retrieve_body( $responseFromvalidateUser );
		
			if ( ! empty( $response ) ) {
				
				if(trim($response) == 'not_found') {					
					//User is not found
					$strResponseMessage = "User name or Password is wrong! Please confirm & validate again.";
					$account_data = array('username'   => $this->username, 'password' => $this->password);
					update_option('cc_account_user_details', $account_data);
					update_option( 'cc_account_status', 'not_found' );
				}
				elseif (trim($response) == '2' )  {
					//User found & product is purchased
					$strResponseMessage = "Settings saved";
					$account_data = array('username'   => $this->username, 'password' => $this->password);
					update_option('cc_account_user_details', $account_data);
					update_option( 'cc_account_status', 'found' );
				}
				elseif (trim($response) == '1' )  {
					//User found - not purchased
					$strResponseMessage = "Settings saved";
					$account_data = array('username'   => $this->username, 'password' => $this->password);
					update_option('cc_account_user_details', $account_data);
					update_option( 'cc_account_status', 'not_valid' );
				}
			}
		}
		else
		{
			$strResponseMessage = "Some issue";
		}
		return $strResponseMessage;
	}
}
	
endif;