<?php

namespace controllers;

use models\CommentModel;

class CommentController extends Controller {
    public function getAll(int $limit, int $offsetID) {
        $commentModel = new CommentModel();
        
        header('Content-Type: application/json');
        
        try {
            $results = $commentModel->getAll($limit, $offsetID);
        } catch(\Throwable $th) {
        }

        if (!isset($results) || $results === FALSE || is_string($results)) {
            http_response_code(400);

            $results = [
                0 => [
                    'error' => $results ?? 'Unknown error',
                ]
            ];
        } else {
            if (count($results) === $limit + 1) {
                $next = $results[$limit]['id'];
                unset($results[$limit]);
            }
        }

        $resultPresentation = [
            'comments' => $results,
            'result_count' => count($results),
        ];

        if (isset($next)) $resultPresentation['comments_next_id'] = $next;

        echo json_encode($resultPresentation, JSON_UNESCAPED_UNICODE);
    }

    public function new()
    {
        $requestBody = file_get_contents('php://input');

        header('Content-Type: application/json');

        $requestArray = json_decode($requestBody, true);

        if (!$requestArray) {
            http_response_code(400);

            $result = [
                'message' => 'Invalid payload',
            ];
        } else {
            $commentModel = new CommentModel();
            try {
                $id = $commentModel->create($requestArray);
            } catch (\Throwable $th){}

            if (isset($id) && is_int($id)) {
                $result = [
                    'id' => $id
                ];
            } else {
                $result = [
                    'message' => 'Unknown error',
                ];
            }
        }
        
        echo json_encode($result);
    }
}