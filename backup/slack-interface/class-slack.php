<?php
namespace Slack_Interface;

require_once 'Requests-master/library/Requests.php';


// Define Slack application identifiers
// Even better is to put these in environment variables so you don't risk exposing
// them to the outer world (e.g. by committing to version control)
define( 'SLACK_CLIENT_ID', '24016979477.123862040083' );
define( 'SLACK_CLIENT_SECRET', '22472be49ad8b91132fdc3ec0b2e8a6f' );
 
/**
 * A basic Slack interface you can use as a starting point
 * for your own Slack projects.
 */
class Slack {
 
    private static $api_root = 'https://slack.com/api/';
 
    /**
	 * @var Slack_Access    Slack authorization data
	 */
	private $access;
	 
	/**
	 * Sets up the Slack interface object.
	 *
	 * @param array $access_data An associative array containing OAuth
	 *                           authentication information. If the user
	 *                           is not yet authenticated, pass an empty array.
	 */
	public function __construct( $access_data ) {
	    if ( $access_data ) {
	        $this->access = new Slack_Access( $access_data );
	    }
	}

	/**
	 * Checks if the Slack interface was initialized with authorization data.
	 *
	 * @return bool True if authentication data is present. Otherwise false.
	 */
	public function is_authenticated() {
	    return isset( $this->access ) && $this->access->is_configured();
	}

    /**
	 * Returns the Slack client ID.
	 * 
	 * @return string   The client ID or empty string if not configured 
	 */
	public function get_client_id() {
	    // First, check if client ID is defined in a constant
	    if ( defined( 'SLACK_CLIENT_ID' ) ) {
	        return SLACK_CLIENT_ID;
	    }
	 
	    // If no constant found, look for environment variable
	    if ( getenv( 'SLACK_CLIENT_ID' ) ) {
	        return getenv( 'SLACK_CLIENT_ID' );
	    }
	         
	    // Not configured, return empty string
	    return '';
	}
	 
	/**
	 * Returns the Slack client secret.
	 * 
	 * @return string   The client secret or empty string if not configured
	 */
	private function get_client_secret() {
	    // First, check if client secret is defined in a constant
	    if ( defined( 'SLACK_CLIENT_SECRET' ) ) {
	        return SLACK_CLIENT_SECRET;
	    }
	 
	    // If no constant found, look for environment variable
	    if ( getenv( 'SLACK_CLIENT_SECRET' ) ) {
	        return getenv( 'SLACK_CLIENT_SECRET' );
	    }
	 
	    // Not configured, return empty string
	    return '';
	}

	/**
	 * Completes the OAuth authentication flow by exchanging the received
	 * authentication code to actual authentication data.
	 *
	 * @param string $code  Authentication code sent to the OAuth callback function
	 *
	 * @return bool|Slack_Access    An access object with the authentication data in place
	 *                              if the authentication flow was completed successfully.
	 *                              Otherwise false.
	 *
	 * @throws Slack_API_Exception 
	 */
	public function do_oauth( $code ) {
	    // Set up the request headers
	    $headers = array( 'Accept' => 'application/json' );
	         
	    // Add the application id and secret to authenticate the request
	    $options = array( 'auth' => array( $this->get_client_id(), $this->get_client_secret() ) );
	 
	    // Add the one-time token to request parameters 
	    $data = array( 'code' => $code );
	 
	    $response = Requests::post( self::$api_root . 'oauth.access', $headers, $data, $options );
	         
	    // Handle the JSON response
	    $json_response = json_decode( $response->body );
	 
	    if ( ! $json_response->ok ) {
	        // There was an error in the request
	        throw new Slack_API_Exception( $json_response->error );
	    }
	 
	    // The action was completed successfully, store and return access data
	    $this->access = new Slack_Access(
	        array(
	            'access_token' => $json_response->access_token,
	            'scope' => explode( ',', $json_response->scope ),
	            'team_name' => $json_response->team_name,
	            'team_id' => $json_response->team_id,
	            'incoming_webhook' => $json_response->incoming_webhook
	        )
	    );
	 
	    return $this->access;
	}
 
}