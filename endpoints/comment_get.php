<?php

function api_comment_get($request) {
  $post_id = $request['id'];

  $comments = get_comments([
    'post_id' => $post_id,
  ]);

  return rest_ensure_response($comments);
}

function register_api_comment_get() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_comment_get',
  ]);
}
add_action('rest_api_init', 'register_api_comment_get');

?>