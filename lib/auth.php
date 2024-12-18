<?php

use App\Response\CustomResponse;


$authenticate = function ($request, $response, $next) {
    $customResponse = new CustomResponse();
    // Getting request headers
    // $headers = apache_request_headers();
    $authorization_header = $request->getHeader("authorization")[0];
    // $headers = $request->getHeader();
    $resp = array();

    // Verifying Authorization Header
    if (isset($authorization_header)) {
        $db = new dbHandler();

        // get the api key
        $api_key = $authorization_header;
        if (preg_match('/Bearer\s(\S+)/', $api_key, $matches)) {
            // echo $matches[1];
            $api_key = $matches[1];
            // $_api_key = str_replace(":", "", base64_decode($api_key));
            $_api_key = str_replace(":", "", ($api_key));
            // $_api_key = $api_key;

            // validating api key
            if (!$db->isValidApiKey($_api_key)) {
                // api key is not present in users table
                $responseMessage = "Access Denied. Invalid Api key";
                return $customResponse->is401Response($response, $responseMessage);
            } else {
                global $user_id;
                // get user primary key id
                $user_id = $db->getIdUser($_api_key);
                $request = $request->withAttribute('user_id', $user_id);
                $db = null;
                $response = $response = $next($request, $response);
            }
            // }
        } else {
            // api key is missing in header
            $responseMessage = "Api key is misssing";
            return $customResponse->is401Response($response, $responseMessage);
        }
    } else {
        // Clé API est absente dans la en-tête
        $responseMessage = "Api key is misssing";
        return $customResponse->is401Response($response, $responseMessage);
    }
    return $response;
};
