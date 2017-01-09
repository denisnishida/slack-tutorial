<?php
namespace Slack_Interface;
 
/**
 * A class for holding Slack authentication data. 
 */
class Slack_Access {

	// Slack OAuth data
 
	private $access_token;
	private $scope;
	private $team_name;
	private $team_id;
	private $incoming_webhook;
 
    public function __construct( $data ) {
 		$this->access_token = isset( $data['access_token'] ) ? $data['access_token'] : '';
	    $this->scope = isset( $data['scope'] ) ? $data['scope'] : array();
	    $this->team_name = isset( $data['team_name'] ) ? $data['team_name'] : '';
	    $this->team_id = isset( $data['team_id'] ) ? $data['team_id'] : '';
	    $this->incoming_webhook = isset( $data['incoming_webhook'] ) ? $data['incoming_webhook'] : array();
    }

    /**
	 * Checks if the object has been initialized with access data.
	 *
	 * @return bool True if authentication data has been stored in the object. Otherwise false.
	 */
	public function is_configured() {
	    return $this->access_token != '';
	}
 
	 /**
	 * Returns the authorization data as a JSON formatted string.
	 *
	 * @return string   The data in JSON format
	 */
	public function to_json() {
	    $data = array(
	        'access_token' => $this->access_token,
	        'scope' => $this->scope,
	        'team_name' => $this->team_name,
	        'team_id' => $this->team_id,
	        'incoming_webhook' => $this->incoming_webhook
	    );
	 
	    return json_encode( $data );
	}
}