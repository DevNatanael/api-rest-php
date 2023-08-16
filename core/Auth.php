<?php

namespace Core;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
	private static function getAuthorization()
	{
		$headers = apache_request_headers();

		if (!empty($headers["Authorization"]))
			return $headers["Authorization"];

		return $headers["authorization"];
	}

	public static function getPayload()
	{
		$auth = self::getAuthorization();
		$token = explode(" ", $auth)[1] ?? '';
		$data = JWT::decode($token, new Key(SECRET, 'HS256'));

		return $data;
	}

	public function __invoke($request, $response)
	{
		$auth = self::getAuthorization();
		$headers = apache_request_headers();
		$token = explode(" ", $auth)[1] ?? '';

		if (empty($token)) {
			return $response->json([
				"status" => false,
				"error" => "Token não provido",
			], 403);
		}

		try {
			$data = JWT::decode($token, new Key(SECRET, 'HS256'));
		} catch (\Exception $e) {
			return $response->json([
				"status" => false,
				"error" => "Token não valido",
			], 403);
		}
	}
}
