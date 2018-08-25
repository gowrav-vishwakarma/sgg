<?php
namespace oauth;
class Controller_OAuth_LinkedIn extends Controller_OAuth {
    protected $type = "linkedin";
    protected $request_token_baseurl = "https://api.linkedin.com/uas/oauth/requestToken";
    protected $access_token_baseurl = "https://api.linkedin.com/uas/oauth/accessToken";
    protected $authorize_token_baseurl = "https://www.linkedin.com/uas/oauth/authorize";
    protected $obtain_request_token_method = "GET";

  function performLinkedInRequest($url, $post_data = null, $get = false){
        $options = array(
            "oauth_token" => urlencode($this->getAuthToken(false)),
        );
        if ($get){
            return $this->performRequest($url, $options);
        } else {
            return $this->performPostRequest($url, array("Expect:"), $options, $post_data);
        }
    }
    function check($scope=null){
        $this->request_token_extra_auth = array("scope" => $scope);
        return parent::check();
    }
}
