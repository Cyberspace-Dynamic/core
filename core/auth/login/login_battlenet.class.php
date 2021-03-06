<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class login_battlenet extends gen_class {
	private $oauth_loaded = false;
	
	private $appid, $appsecret = false;
	
	private $AUTHORIZATION_ENDPOINT = 'https://{region}.battle.net/oauth/authorize';
	private $TOKEN_ENDPOINT         = 'https://{region}.battle.net/oauth/token';
	private $CHECK_TOKEN          = 'https://{region}.battle.net/oauth/check_token?token={token}';
	
	public static $functions = array(
		'login_button'		=> 'login_button',
		'account_button'	=> 'account_button',
		'get_account'		=> 'get_account',
	);
	
	public static $options = array(
		'connect_accounts'	=> true,	
	);
	
	public function __construct(){
		$region = $this->config->get('uc_server_loc');
		if(!$region) $region = 'eu';		
		switch($region){
			case 'eu' :
				$region = 'eu';
				$this->TOKEN_ENDPOINT = str_replace('{region}', $region, $this->TOKEN_ENDPOINT);
				$this->AUTHORIZATION_ENDPOINT = str_replace('{region}', $region, $this->AUTHORIZATION_ENDPOINT);
				$this->CHECK_TOKEN  = str_replace('{region}', $region, $this->CHECK_TOKEN);
				break;
			case 'us' :
				$region = 'us';
				$this->TOKEN_ENDPOINT = str_replace('{region}', $region, $this->TOKEN_ENDPOINT);
				$this->AUTHORIZATION_ENDPOINT = str_replace('{region}', $region, $this->AUTHORIZATION_ENDPOINT);
				$this->CHECK_TOKEN  = str_replace('{region}', $region, $this->CHECK_TOKEN);
				break;
			case 'tw' :
				$region = 'apac';
				$this->TOKEN_ENDPOINT = str_replace('{region}', $region, $this->TOKEN_ENDPOINT);
				$this->AUTHORIZATION_ENDPOINT = str_replace('{region}', $region, $this->AUTHORIZATION_ENDPOINT);
				$this->CHECK_TOKEN  = str_replace('{region}', $region, $this->CHECK_TOKEN);
				break;
			case 'kr' :
				$region = 'apac';
				$this->TOKEN_ENDPOINT = str_replace('{region}', $region, $this->TOKEN_ENDPOINT);
				$this->AUTHORIZATION_ENDPOINT = str_replace('{region}', $region, $this->AUTHORIZATION_ENDPOINT);
				$this->CHECK_TOKEN  = str_replace('{region}', $region, $this->CHECK_TOKEN);
				break;
			case 'cn' :
				$region = 'eu';
				$this->TOKEN_ENDPOINT = "https://www.battlenet.com.cn/oauth/token";
				$this->AUTHORIZATION_ENDPOINT = "https://www.battlenet.com.cn/oauth/authorize";
				$this->CHECK_TOKEN  = "https://www.battlenet.com.cn/oauth/check_token?token={token}";
				break;
		}		
	}
	
	public function settings(){
		$settings = array(
			'login_bnet_appid'	=> array(
				'type'	=> 'text',
			),
			'login_bnet_appsecret' => array(
				'type'	=> 'text',
			),
		);
		return $settings;
	}
	

	
	public function init_oauth(){
		if (!$this->oauth_loaded){
			if(!class_exists('OAuth2\\Exception')) {
				require($this->root_path.'libraries/oauth/Client.php');
				require($this->root_path.'libraries/oauth/GrantType/IGrantType.php');
				require($this->root_path.'libraries/oauth/GrantType/AuthorizationCode.php');
			}
			
			$this->appid = $this->config->get('login_bnet_appid');
			$this->appsecret = $this->config->get('login_bnet_appsecret');
			
			$this->oauth_loaded = true;
		
		}
	}
	
	public function login_button(){
		$this->init_oauth();
		
		$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=battlenet';

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'wow.profile'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin battlenet loginbtn" onclick="window.location=\''.$auth_url.'\'"><i class="bi_battlenet"></i> Battle.net</button>';
	}
	
	
	public function account_button(){
		$this->init_oauth();
		
		$redir_url = $this->env->buildLink().'index.php/Settings/?mode=addauthacc&lmethod=battlenet';

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'wow.profile'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin battlenet accountbtn" onclick="window.location=\''.$auth_url.'\'"><i class="bi_battlenet"></i> Battle.net</button>';		
	}
	
	public function get_account(){
		$this->init_oauth();
		
		$code = $this->in->get('code');
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			$redir_url = $this->env->buildLink().'index.php/Settings/?mode=addauthacc&lmethod=battlenet';
			
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'wow.profile');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			
			if ($response && $response['result'] && $response['result']['access_token']){
				
				$accountResponse = register('urlfetcher')->fetch(str_replace('{token}', $response['result']['access_token'], $this->CHECK_TOKEN));
				if($accountResponse){
					$arrAccountResult = json_decode($accountResponse, true);
					if(isset($arrAccountResult['user_name'])){
						return $arrAccountResult['user_name'];
					}
				}
			}
		}

		return false;
	}
	
	
	
	/**
	* User-Login for Facebook
	*
	* @param $strUsername
	* @param $strPassword
	* @param $boolUseHash Use Hash for comparing
	* @return bool/array	
	*/	
	public function login($strUsername, $strPassword, $boolUseHash = false){
		$blnLoginResult = false;
		
		
		$this->init_oauth();
		
		$code = $_GET['code'];
	
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=battlenet';

			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'wow.profile');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result']){
					
				$accountResponse = register('urlfetcher')->fetch(str_replace('{token}', $response['result']['access_token'], $this->CHECK_TOKEN));
				if($accountResponse){
					$arrAccountResult = json_decode($accountResponse, true);
					if(isset($arrAccountResult['user_name'])){
						$userid = $this->pdh->get('user', 'userid_for_authaccount', array($arrAccountResult['user_name'], 'battlenet'));
						if ($userid){
							$userdata = $this->pdh->get('user', 'data', array($userid));
							if ($userdata){
								list($strPwdHash, $strSalt) = explode(':', $userdata['user_password']);
								return array(
										'status'		=> 1,
										'user_id'		=> $userdata['user_id'],
										'password_hash'	=> $strPwdHash,
										'autologin'		=> true,
										'user_login_key' => $userdata['user_login_key'],
								);
							}
						}
						
					}
				}

			}
		}
		
		return false;
	}
	
	/**
	* User-Logout
	*
	* @return bool
	*/
	public function logout(){
		return true;
	}
	
	/**
	* Autologin
	*
	* @param $arrCookieData The Data ot the Session-Cookies
	* @return bool
	*/
	public function autologin($arrCookieData){		
		return false;
	}
}
?>