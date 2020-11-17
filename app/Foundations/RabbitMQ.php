<?php

namespace App\Foundations;

use App\Modules\Common\DingDing;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQ
{
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel;
     */
    protected $channel;
    protected $exchange;
    protected $queue;

    public function __construct()
    {
        $this->conn();
    }

    public function conn($exchange = '', $host = '', $port = '', $user = '', $pwd = '', $vhost = '')
    {
        $host = $host ?: config('app.amqp_mq.host');
        $port = $port ?: config('app.amqp_mq.port');
        $user = $user ?: config('app.amqp_mq.user');
        $password = $pwd ?: config('app.amqp_mq.pwd');
        $vhost = $vhost ?: config('app.amqp_mq.vhost');

        $this->exchange = $exchange ?: config('app.amqp_mq.exchange');
        $this->queue = config('app.amqp_mq.queue');

        $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $this->channel = $connection->channel();
    }

    public function declare()
    {
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, 'fanout', false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);
    }

    public function publish($msg)
    {
        if (!$this->channel->is_open()) {
            $this->conn();
        }
        try {
            $mqMsg = new AMQPMessage(
                $msg,
                array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
            );
            $this->channel->basic_publish($mqMsg, $this->exchange);
        } catch (\Exception $e) {
            $this->conn();
            try {
                $this->channel->basic_publish($msg, $this->exchange);
            } catch (\Exception $exception) {
                Log::error(
                    "ampq-send-failed",
                    ['msgData' => $msg, 'errCode' => $exception->getCode(), 'errMsg' => $exception->getMessage()]
                );
//                ding(DingDing::genMsg("发送到rabbitMQ失败", $msg, $exception->getCode(), $exception->getMessage()));
            }
        }
    }

    /**
     * 获取队列消息
     * @param string $queueName
     * @return string|null
     * @throws \Exception
     */
    public function fetch(string $queueName = '')
    {
        $message = $this->channel->basic_get($queueName);
        if (!empty($message)) {
            try {
                $this->channel->basic_ack($message->delivery_info['delivery_tag']);
            } catch (AMQPChannelClosedException $ex) {
                Log::error('amqp-fetch:error' . $ex->__toString());
                return false;
            } catch (\Exception $ex) {
                Log::error('amqp-fetch:error' . $ex->__toString());
                return false;
            }
            $body = $message->body;
            return $body;
        }
        return null;
    }
}
