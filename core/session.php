<?php

function create_session(array $session) {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	session_regenerate_id(true);
	$_SESSION = $session;
}

function destroy_session() {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	$_SESSION = [];
	session_destroy();
}

function get_session() {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	return $_SESSION ?: null;
}

function session_include(string $redirect = '/login', int $response_code = 302) {
	$session = get_session();

	if (!is_array($session)) {
		redirect($redirect, $response_code);
		return;
	}

	return $session;
}

function session_exclude(string $redirect = '/', int $response_code = 302) {
	$session = get_session();

	if (is_array($session)) {
		redirect($redirect, $response_code);
		return;
	}

	return $session;
}

function role_include(array|string $list, string $redirect = '/403', int $response_code = 403) {
	$session = session_include();

	if (!in_array($session['role'] ?? null, (array) $list, true)) {
		redirect($redirect, $response_code);
	}
}

function role_exclude(array $list, string $redirect = '/403', int $response_code = 403) {
	$session = session_include();

	if (in_array($session['role'] ?? null, $list, true)) {
		redirect($redirect, $response_code);
	}
}