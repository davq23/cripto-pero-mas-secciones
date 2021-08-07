<?php

namespace controllers;

use models\CommentModel;
use utils\XMLSerializer;

class CommentController extends Controller {
    public function getAll(string $format, int $limit, int $offsetID) {
        $commentModel = new CommentModel();
        
        header($format === 'json' ? 'Content-Type: application/json' : 'Content-Type: text/xml');
        
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

        switch ($format) {
            case 'json':
                $resultPresentation = [
                    'comments' => $results,
                    'result_count' => count($results),
                ];

                if (isset($next)) $resultPresentation['comments_next_id'] = $next;

                echo json_encode($resultPresentation, JSON_UNESCAPED_UNICODE);
                break;
            
            case 'xml':
                $resultPresentation = XMLSerializer::serializeArray($results, 'comments', 'comment');
                $resultPresentation->childNodes[0]->setAttribute('result_count', count($results));
                
                if (isset($next)) $resultPresentation->childNodes[0]->setAttribute('comments_next_id', $next);

                echo $resultPresentation->saveXML();
                break;
        }
    }

    public function new(string $format)
    {
        $requestBody = file_get_contents('php://input');

        header($format === 'json' ? 'Content-Type: application/json' : 'Content-Type: text/xml');

        $rootName = '';

        switch($format) {
            case 'json':
                $requestArray = json_decode($requestBody, true);
                break;
                
            case 'xml':
                $requestArray = simplexml_load_string($requestBody);
                $requestArray = json_encode($requestArray, JSON_UNESCAPED_UNICODE);
                $requestArray = json_decode($requestArray, JSON_UNESCAPED_UNICODE);

                break;
        }

        if (!$requestArray) {
            http_response_code(400);

            $result = [
                'message' => 'Invalid payload',
            ];

            $rootName = 'error';
        } else {
            $commentModel = new CommentModel();
            try {
                $id = $commentModel->create($requestArray);
            } catch (\Throwable $th){}

            if (isset($id) && is_int($id)) {
                $rootName = 'comment';

                $result = [
                    'id' => $id
                ];
            } else {
                $rootName = 'error';

                $result = [
                    'message' => 'Unknown error',
                ];
            }
        }
        
        echo $format === 'json' ? json_encode($result) : XMLSerializer::serializeArrayOne($result, $rootName)->saveXML();
    }
}