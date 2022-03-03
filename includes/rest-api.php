<?php

namespace Responic_Donasiaja;


class Rest_Api
{

    public function register_route()
    {
        register_rest_route('responic-donasiaja', '/send', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handle'],
        ));

        register_rest_route('responic-donasiaja', '/sendfile', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handlefile'],
        ));
    }

    public function handle(\WP_REST_Request $request)
    {

        $params = $request->get_body_params();

        $apikey = $params['Apikey'];
        $phone = $params['Phone'];
        $message = $params['Message'];

        error_log('responic_notif : recipient ' . $phone . ' is valid');
        error_log('responic_notif : token ready ' . $apikey);
        error_log('responic_notif : message ' . $message);

        $response = wp_remote_post(
            'https://api.responic.com/message/text',
            [
                'timeout' => 50,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'body' => wp_json_encode([
                    'recipient' => $phone,
                    'message' => rawurlencode($message),
                ]),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apikey
                ]
            ]
        );

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $code = intval(wp_remote_retrieve_response_code($response));

        error_log('responic_send_status : ' . wp_json_encode($body));
        error_log('responic_send_status_code : ' . $code);

        return new \WP_REST_Response([
            'wanotif' => [
                'status' => $code == 200 ? 'sent' : 'failed',
            ]
        ], $code);
    }

    public function handlefile(\WP_REST_Request $request)
    {
        $params = $request->get_body_params();

        $apikey = $params['Apikey'];
        $phone = $params['Phone'];
        $message = $params['Message'];

        error_log('responic_notif : recipient ' . $phone . ' is valid');
        error_log('responic_notif : token ready ' . $apikey);
        error_log('responic_notif : message ' . $message);

        $response = wp_remote_post(
            'https://api.responic.com/message/text',
            [
                'timeout' => 50,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'body' => wp_json_encode([
                    'recipient' => $phone,
                    'message' => rawurlencode($message),
                ]),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apikey
                ]
            ]
        );

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $code = intval(wp_remote_retrieve_response_code($response));

        error_log('responic_send_status : ' . wp_json_encode($body));
        error_log('responic_send_status_code : ' . $code);

        return new \WP_REST_Response($body, $code);
    }
}
