<?php

namespace Github\Api\Organization;

use Github\Api\AbstractApi;

/**
 * @link   https://docs.github.com/en/rest/orgs/organization-roles
 */
class OrganizationRoles extends AbstractApi
{
    public function all(string $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/organization-roles');
    }

    public function show(string $organization, int $roleId)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/organization-roles/'.$roleId);
    }

    public function listTeamsWithRole(string $organization, int $roleId)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/organization-roles/'.$roleId.'/teams');
    }

    public function assignRoleToTeam(string $organization, int $roleId, string $teamSlug): void
    {
        $this->put('/orgs/'.rawurlencode($organization).'/organization-roles/teams/'.rawurlencode($teamSlug).'/'.$roleId);
    }

    public function removeRoleFromTeam(string $organization, int $roleId, string $teamSlug): void
    {
        $this->delete('/orgs/'.rawurlencode($organization).'/organization-roles/teams/'.rawurlencode($teamSlug).'/'.$roleId);
    }

    public function removeAllRolesFromTeam(string $organization, string $teamSlug): void
    {
        $this->delete('/orgs/'.rawurlencode($organization).'/organization-roles/teams/'.rawurlencode($teamSlug));
    }

    public function listUsersWithRole(string $organization, int $roleId): array
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/organization-roles/'.$roleId.'/users');
    }

    public function assignRoleToUser(string $organization, int $roleId, string $username): void
    {
        $this->put('/orgs/'.rawurlencode($organization).'/organization-roles/users/'.rawurlencode($username).'/'.$roleId);
    }

    public function removeRoleFromUser(string $organization, int $roleId, string $username): void
    {
        $this->delete('/orgs/'.rawurlencode($organization).'/organization-roles/users/'.rawurlencode($username).'/'.$roleId);
    }

    public function removeAllRolesFromUser(string $organization, string $username): void
    {
        $this->delete('/orgs/'.rawurlencode($organization).'/organization-roles/users/'.rawurlencode($username));
    }
}
