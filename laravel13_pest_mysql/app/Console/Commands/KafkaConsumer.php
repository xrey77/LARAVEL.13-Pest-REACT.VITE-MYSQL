<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Config\Sasl;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\MessageConsumer;

class KafkaConsumer extends Command
{
    // Laravel 13 supports native PHP attributes for command definitions
    #[Signature('kafka:consume')]
    #[Description('Consume messages from Kafka')]
    protected $signature = 'kafka:consume';

    public function handle()
    {
        $consumer = Kafka::consumer(['central-topic'])
            ->withBrokers(env('KAFKA_BROKERS', 'localhost:9092'))
            ->withConsumerGroupId('lionheart-group') // Required for offset tracking
            ->withSasl(new Sasl(
                username: env('KAFKA_SASL_USERNAME'),
                password: env('KAFKA_SASL_PASSWORD'),
                mechanisms: env('KAFKA_SASL_MECHANISM', 'PLAIN'),
                securityProtocol: env('KAFKA_SECURITY_PROTOCOL', 'SASL_SSL')
            ))
            ->withHandler(function(ConsumerMessage $message, MessageConsumer $consumer) {
                // In Laravel 13, $message->getBody() returns the decoded payload
                $this->info("Received: " . json_encode($message->getBody()));
            })
            ->build();

        $consumer->consume();
    }
}
