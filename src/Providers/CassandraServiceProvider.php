<?php
namespace Unisharp\Cassandra\Providers;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Cassandra;
use Unisharp\Cassandra\Connection;

class CassandraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '../../config/database.php', 'database');
        $this->app->resolving('db', function (DatabaseManager $databaseManager) {
            $databaseManager->extend('cassandra', function ($config, $name) {
                return new Connection(
                    $this->getCluster($config),
                    $this->getDatabase($config),
                    $this->getPrefix($config),
                    $config
                );
            });
        });
    }

    public function boot()
    {

    }

    public function getDatabase(array $config)
    {
        if (isset($config['keyspace'])) {
            return $config['keyspace'];
        } elseif (isset($config['database'])) {
            return $config['database'];
        }

        return '';
    }

    public function getPrefix(array $config)
    {
        return isset($config['prefix']) ? $config['prefix'] : '';
    }

    public function getCluster(array $config)
    {
        $cluster = Cassandra::cluster();

        if (!isset($options['username']) && !empty($config['username'])) {
            $options['username'] = $config['username'];
        }
        if (!isset($options['password']) && !empty($config['password'])) {
            $options['password'] = $config['password'];
        }

        if (isset($options['username']) && isset($options['password'])) {
            $cluster->withCredentials($options['username'], $options['password']);
        }

        if (isset($options['contactpoints']) || (isset($config['host']) && !empty($config['host']))) {
            $contactPoints = $config['host'];

            if (isset($options['contactpoints'])) {
                $contactPoints = $options['contactpoints'];
            }

            $cluster->withContactPoints($contactPoints);
        }

        if (!isset($options['port']) && !empty($config['port'])) {
            $cluster->withPort((int) $config['port']);
        }

        return $cluster->build();
    }
}
