<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\KafkaProducerService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KafkaProducerService::class, function ($app) {
            $config = config('kafka.connections.default', [
                'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
            ]);            
            
            if (empty($config)) {
                throw new \Exception("Kafka configuration 'kafka.connections.default' is missing.");
            }

            return new KafkaProducerService($config);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
