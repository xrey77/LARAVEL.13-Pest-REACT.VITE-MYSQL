<?php

namespace App\Services;

use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Config\Sasl;
use Junges\Kafka\Message\Message;

class KafkaProducerService
{
    /**
     * Publish a message to a specific Kafka topic.
     */
    public function publishMessage(string $topic, array $data, ?string $key = null)
    {
        $message = new Message(
            body: $data,
            key: $key
        );

        return Kafka::publish()
            ->onTopic($topic)
            ->withMessage($message)
            ->send();
    }
}
