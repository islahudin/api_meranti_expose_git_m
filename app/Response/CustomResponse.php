<?php

namespace App\Response;

use Respect\Validation\Exceptions\WritableException;

class CustomResponse
{

    public function is200Response2($response, $responseMessage, string $status = "OK", $rPagination = array())
    {
        $_arrayStatus = ["success" => true, "code" => 200, "status" => $status];
        $_arrayData = ["data" => $responseMessage, "error" => null];
        $_arrayAll = array_merge($_arrayStatus, $rPagination, $_arrayData);

        // return $response->withStatus(200)
        //     ->withHeader("Content-Type", "application/json")
        //     ->write(json_encode($_arrayAll, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $responseMessage = json_encode($_arrayAll, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json; charset=utf-8")
            ->withStatus(200);

        // $responseMessage = json_encode($_arrayAll);
        // $response->getBody()->write($responseMessage);
        // return $response->withStatus(200);

        // return $response->withJson($_arrayAll, 200, JSON_UNESCAPED_UNICODE);
        // return $response->withStatus(200)
        //     ->withHeader('Content-Type', 'application/json')
        //     ->write(json_encode($_arrayAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        // return $response->withJson($_arrayAll);
        // return $response->withJson($responseMessage)->withHeader('Content-Type', 'application/json');
        // return $response->write(json_encode($responseMessage, JSON_PRETTY_PRINT))
        //     ->withHeader('Content-Type', 'application/json;charset=utf-8');
        // return $response->withStatus(200)
        //     ->withHeader('Content-Type', 'application/json;charset=utf-8')
        //     ->withJson($_arrayAll);

    }

    // public function is200Response($response, $responseMessage, $error = null, $status = "OK")
    // {
    //     $responseMessage = json_encode(["success" => true, "code" => 200, "status" => $status, "data" => $responseMessage, "error" => $error]);
    //     $response->getBody()->write($responseMessage);
    //     return $response->withHeader("Content-Type", "application/json")
    //         ->withStatus(200);
    // }

    public function is200Response($response, $responseMessage, $status = "OK", $rPagination = array())
    {
        $_arrayStatus = ["success" => true, "code" => 200, "status" => $status];
        $_arrayData = ["data" => $responseMessage, "error" => null];
        $_arrayAll = array_merge($_arrayStatus, $rPagination, $_arrayData);
        $responseMessage = json_encode($_arrayAll, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(200);
    }


    public function is400Response($response, $responseMessage = "", $status = "Bad Request")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 400, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(400);
    }

    public function is401Response($response, $responseMessage = "", $status = "Unauthorized")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 401, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(401);
    }

    public function is403Response($response, $responseMessage = "", $status = "Forbidden")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 403, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(403);
    }

    public function is404Response($response, $responseMessage = "", $status = "Not Found")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 404, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(404);
    }

    public function is405Response($response, $responseMessage = "", $status = "Method Not Allowed")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 405, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(405);
    }

    public function is409Response($response, $responseMessage = "", $status = "Conflict")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 409, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(409);
    }

    public function is422Response($response, $responseMessage = "", $status = "Unprocessable Entity")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 422, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(422);
    }

    public function is500Response($response, $responseMessage = "", $status = "Internal Server Error")
    {
        $error = empty($responseMessage) ? $status : $responseMessage;
        $responseMessage = json_encode(["success" => false, "code" => 500, "status" => $status, "data" => null, "error" => $error]);
        $response->getBody()->write($responseMessage);
        return $response->withHeader("Content-Type", "application/json")
            ->withStatus(500);
    }
}
