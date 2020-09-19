<?php

function api_password_lost($request) {
  $login = $request['login'];
  $url = $request['url'];

  if (empty($login)) {
    $response = new WP_Error('error', 'Informe o email ou login.', ['status' => 406]);
    return rest_ensure_response($response);
  }
  $user = get_user_by('email', $login);
  if (empty($user)) {
    $user = get_user_by('login', $login);
  }
  if (empty($user)) {
    $response = new WP_Error('error', 'Usuário não existe.', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $user_login = $user->user_login;
  $user_email = $user->user_email;
  $key = get_password_reset_key($user);

  $message = "Utilize o link abaixo para resetar a sua senha: \r\n";
  $url = esc_url_raw($url . "/?key=$key&login=" . rawurlencode($user_login) . "\r\n");
  $body = $message . $url;

  wp_mail($user_email, 'Password Reset', $body);

  return rest_ensure_response('Email enviado.');
}

function register_api_password_lost() {
  register_rest_route('api', '/password/lost', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_password_lost',
  ]);
}
add_action('rest_api_init', 'register_api_password_lost');

// Password Reset

function api_password_reset($request) {
  $login = $request['login'];
  $password = $request['password'];
  $key = $request['key'];
  $user = get_user_by('login', $login);

  if (empty($user)) {
    $response = new WP_Error('error', 'Usuário não existe.', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $check_key = check_password_reset_key($key, $login);

  if (is_wp_error($check_key)) {
    $response = new WP_Error('error', 'Token expirado.', ['status' => 401]);
    return rest_ensure_response($response);
  }

  reset_password($user, $password);

  return rest_ensure_response('Senha alterada.');
}

function register_api_password_reset() {
  register_rest_route('api', '/password/reset', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_password_reset',
  ]);
}
add_action('rest_api_init', 'register_api_password_reset');

?>