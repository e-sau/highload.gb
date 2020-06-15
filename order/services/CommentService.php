<?php


namespace App\services;

use App\models\Comment;

require_once (__DIR__ . '/../../vendor/autoload.php');
$config = require(__DIR__ . '/../config/config.php');

class CommentService extends BaseService
{
    protected $exchange = 'order';
    protected $binding_key = 'new_comment';
    protected $config;

    protected function getCallback()
    {
        return function ($msg) {
            if (!empty($msg)) {
                file_put_contents(
                    'php://stdout',
                    ' [x] ' . $msg->delivery_info['routing_key'] . "\n"
                );

                $data = json_decode($msg->body, true);
                $comment_id = $this->createComment($data);

                if ($comment_id) {
                    file_put_contents(
                        'php://stdout',
                        ' [x] Comment #' . $comment_id . ' added' . "\n"
                    );
                }
            }
        };
    }

    public function createComment($data)
    {
        $comment = new Comment();

        $user_id = !empty($data['user_id']) ? $data['user_id'] : null;
        $order_id = !empty($data['order_id']) ? $data['order_id'] : null;
        $message = !empty($data['message']) ? $data['message'] : '';

        if ($user_id && $order_id) {
            $comment->setUserId($user_id);
            $comment->setOrderId($order_id);
            $comment->setMessage($message);

            return $comment->create();

        }
        return false;
    }
}

$commentService = new CommentService($config);
$commentService->run();