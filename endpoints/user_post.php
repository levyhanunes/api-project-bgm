<?php

function api_user_post($request) {
  $email = sanitize_email($request['email']);
  $username = sanitize_text_field($request['username']);
  $password = $request['password'];

  if (empty($email) || empty($username) || empty($password)) {
    $response = new WP_Error('error', 'Dados incompletos', ['status' => 406]);
    return rest_ensure_response($response);
  }

  if (username_exists($username) || email_exists($email)) {
    $response = new WP_Error('error', 'Email já cadastrado', ['status' => 403]);
    return rest_ensure_response($response);
  }

  $response = wp_insert_user([
    'user_login' => $username,
    'user_email' => $email,
    'user_pass' => $password,
    'role' => 'subscriber',
  ]);

  return rest_ensure_response($response);
}

function register_api_user_post() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_user_post',
  ]);
}
add_action('rest_api_init', 'register_api_user_post');

?>