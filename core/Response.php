<?php

namespace Core;
class Response {
	public function json(Array $data, int $status = 200) {
		http_response_code($status);
		echo json_encode($data);
		exit;
	}
}

?>
