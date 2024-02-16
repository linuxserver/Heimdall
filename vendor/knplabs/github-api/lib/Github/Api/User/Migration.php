<?php

namespace Github\Api\User;

use Github\Api\AbstractApi;

class Migration extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#list-user-migrations
     *
     * @param array $params
     *
     * @return array|string
     */
    public function list(array $params = [])
    {
        return $this->get('/user/migrations', $params);
    }

    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#start-a-user-migration
     *
     * @param array $params
     *
     * @return array|string
     */
    public function start(array $params)
    {
        return $this->post('/user/migrations', $params);
    }

    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#get-a-user-migration-status
     *
     * @param int   $migrationId
     * @param array $params
     *
     * @return array|string
     */
    public function status(int $migrationId, array $params = [])
    {
        return $this->get('/user/migrations/'.$migrationId, $params);
    }

    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#delete-a-user-migration-archive
     *
     * @param int $migrationId
     *
     * @return array|string
     */
    public function deleteArchive(int $migrationId)
    {
        return $this->delete('/user/migrations/'.$migrationId.'/archive');
    }

    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#unlock-a-user-repository
     *
     * @param int    $migrationId
     * @param string $repository
     *
     * @return array|string
     */
    public function unlockRepo(int $migrationId, string $repository)
    {
        return $this->delete('/user/migrations/'.$migrationId.'/repos/'.rawurlencode($repository).'/lock');
    }

    /**
     * @link https://docs.github.com/en/rest/migrations/users?apiVersion=2022-11-28#list-repositories-for-a-user-migration
     *
     * @param int   $migrationId
     * @param array $params
     *
     * @return array|string
     */
    public function repos(int $migrationId, array $params = [])
    {
        return $this->get('/user/migrations/'.$migrationId.'/repositories', $params);
    }
}
