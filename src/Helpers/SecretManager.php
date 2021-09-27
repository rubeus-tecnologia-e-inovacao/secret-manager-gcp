<?php

namespace src\Helpers;

// Import the Secret Manager client library.
use Google\Cloud\SecretManager\V1\Replication;
use Google\Cloud\SecretManager\V1\Replication\Automatic;
use Google\Cloud\SecretManager\V1\Secret;
use Google\Cloud\SecretManager\V1\SecretManagerServiceClient;
use Google\Cloud\SecretManager\V1\SecretPayload;

use stdClass;
use Exception;

class SecretManager
{
    /**
     *
     *
    **/

    private static function connect($projectId)
    {
        // Create the Secret Manager client.
        try {
            $connection = new stdClass;
            $credentials = env('GOOGLE_APPLICATION_CREDENTIALS');
            if (empty($credentials)) {
                $connection->client = false;
                $connection->message = "GOOGLE_APPLICATION_CREDENTIALS not found at .env";
            } else {
                $client = new SecretManagerServiceClient();
                $connection->client = $client;
                $connection->message = "Autenticado em $projectId com sucesso";
            }
        } catch (Exception $e) {
            $connection->client = false;
            $connection->message = $e;
        }

        return $connection;

    }


    private static function create($client, $projectId, $secretName)
    {
        // Build the resource name of the parent project.
        $parent = $client->projectName($projectId);

        // Create the secret.
        $secret = $client->createSecret($parent, $secretName,
            new Secret([
                'replication' => new Replication([
                    'automatic' => new Automatic(),
                ]),
            ])
        );

        return $secret;

    }

    public static function list($projectId, $secretName)
    {
        $connection = SecretManager::connect($projectId);

        if ($connection->client) {

            $list = [];

            $formattedParent = $connection->client->secretName($projectId, $secretName);
            $listVersions = $connection->client->listSecretVersions($formattedParent);

            foreach ($listVersions->iterateAllElements() as $element) {
                $response = $connection->client->accessSecretVersion($element->getName());
                $list[] = [
                    "version" => end(explode("/",$element->getName())),
                    "value" => $response->getPayload()->getData()
                ];
            }
            return $list;
        } else {
            return $connection->message;
        }
    }

    public static function get($projectId, $secretName, $version)
    {

        $connection = SecretManager::connect($projectId);

        if ($connection->client) {
            $versionName = $connection->client->secretVersionName($projectId, $secretName, $version);
            $version = $connection->client->accessSecretVersion($versionName);

            // Access the secret version.
            $response = $connection->client->accessSecretVersion($version->getName());

            // Print the secret payload.
            // WARNING: Do not print the secret in a production environment
            $payload = $response->getPayload()->getData();

            return $payload;
        } else {
            return $connection->message;
        }



    }

    public static function getLast($projectId, $secretName)
    {

        $connection = SecretManager::connect($projectId);

        if ($connection->client) {
            $formattedParent = $connection->client->secretName($projectId, $secretName);

            $listVersions = $connection->client->listSecretVersions($formattedParent);
            foreach ($listVersions->iterateAllElements() as $element) {
                $response = $connection->client->accessSecretVersion($element->getName());
                return $response->getPayload()->getData();
            }
        } else {
            return $connection->message;
        }
    }

    public static function put($projectId, $secretName, $versionValue)
    {

        $connection = SecretManager::connect($projectId);

        if ($connection->client) {
            $newSecret = SecretManager::create($connection->client, $projectId, $secretName);

            if ($newSecret) {
                $storeSecret = SecretManager::patch($projectId, $secretName, $versionValue);
                return $storeSecret;
            } else {
                return false;
            }
        } else {
            return $connection->message;
        }
    }

    public static function patch($projectId, $secretName, $newVersionValue)
    {

        $connection = SecretManager::connect($projectId);

        if ($connection->client) {
            // Add the secret version
            $formattedName = $connection->client->secretName($projectId, $secretName);
            $secret = $connection->client->getSecret($formattedName);
            $version = $connection->client->addSecretVersion($secret->getName(), new SecretPayload([
                'data' => $newVersionValue,
            ]));

            return $version;
        } else {
            return $connection->message;
        }
    }

}
