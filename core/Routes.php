<?php

namespace Core;

require "/var/www/web-store-php/core/Response.php";

class Routes
{
    /**
     * routes
     * @type
     */
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];
    private $request;
    private $response;
    private $middlewares = [];
    private $url;
    private $method;

    public function __construct()
    {
        /*
        ?? Este é o operador de coalescência nula. Ele verifica se o valor à esquerda (nesse caso, $_GET['url'])
        é nulo ou indefinido. Se for, ele retorna o valor à direita (neste caso, $_SERVER['REQUEST_URI']).
        */
        $this->url = $_GET['url'] ?? $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->request = new \stdClass;
        $this->response = new Response;
        $this->request->body = json_decode(file_get_contents('php://input')) ?? [];
        /*
        $this->request->body = json_decode(file_get_contents('php://input')) ?? [];
        Lendo o conteúdo do corpo da requisição HTTP.
        Tentando decodificar esse conteúdo como um objeto JSON.
        Se a decodificação for bem-sucedida e resultar em um objeto ou array, atribui esse objeto/array à propriedade $request->body.
        Se a decodificação falhar ou o conteúdo do corpo estiver vazio, atribui um array vazio à propriedade $request->body
        */
    }

    public function get(String $url, String $resolver): void
    {
        $this->addRoute('GET', $url, $resolver);
    }

    public function post(String $url, String $resolver): void
    {
        $this->addRoute('POST', $url, $resolver);
    }

    public function put(String $url, String $resolver): void
    {
        $this->addRoute('PUT', $url, $resolver);
    }

    public function delete(String $url, String $resolver): void
    {
        $this->addRoute('DELETE', $url, $resolver);
    }

    public function run()
    {
        $url = $this->url;
        $queryParams = strpos($url, "?");

        if ($queryParams !== false) {
            $url = substr($url, 0, $queryParams);
        }

        $urlArray = explode('/', $url);
        $found = false;

        foreach ($this->routes[$this->method] as $route) {
            $routeArray = explode('/', $route[0]);
            $params = [];

            for ($i = 0; $i < count($routeArray); $i++) {
                // substitui o parametro da url {}
                if (
                    (strpos($routeArray[$i], "{") !== false) &&
                    (count($urlArray) == count($routeArray))
                ) {
                    $param_name = substr($routeArray[$i], 1, -1);
                    $routeArray[$i] = $urlArray[$i];
                    $params[$param_name] = $urlArray[$i];
                }
            }
            $route[0] = implode('/', $routeArray);

            if ($url === $route[0]) {
                $found = true;
                $this->request->params = $params;
                $this->request->getParams = $_GET;
                $file = $route[1];

                // se não houver middlewares ja sai do loop
                if (count($route) < 3) {
                    break;
                }

                $middlewares = $route[2];

                foreach ($middlewares as $middleware) {
                    $middleware($this->request, $this->response);
                }

                break;
            }
        }

        if (!$found) {
            return $this->response->json([
                'status' => 'error',
                'error' => 'Página não encontrada',
            ], 404);
        }

        if (!strpos($file, ":")) {
            require_once $file;
            return;
        }

        $fileArray = explode(':', $file);
        $file = $fileArray[0];
        $function = $fileArray[1];

        require_once $file;
        $function($this->request, $this->response);
        return;
    }

    private function addRoute(String $type, String $url, String $resolver): void
    {
        $middlewares = $this->middlewares;

        if (count($middlewares)) {
            $this->routes[$type][] = [$url, $resolver, $middlewares];
            return;
        }

        $this->routes[$type][] = [$url, $resolver];
    }

    public function use($middleware)
    {
        $this->middlewares[] = $middleware;
    }
}
