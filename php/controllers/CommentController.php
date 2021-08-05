<?php


class CommentController {
    public function postComment() {
        $jsonVar = json_encode(file_get_contents('php://in'));

        if (empty($jsonVar) || !$jsonVar || !isset($jsonVar['commentBody'])) {
            exit(400);
        }

        
        
    }
}