<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link   https://developer.github.com/v3/repos/branches/
 *
 * @author Brandon Bloodgood <bbloodgood@gmail.com>
 */
class Protection extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Retrieves configured protection for the provided branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#get-branch-protection
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The branch protection information
     */
    public function show($username, $repository, $branch)
    {
        // Preview endpoint
        $this->acceptHeaderValue = 'application/vnd.github.luke-cage-preview+json';

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection');
    }

    /**
     * Updates the repo's branch protection.
     *
     * @link https://developer.github.com/v3/repos/branches/#update-branch-protection
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch protection information
     *
     * @return array The updated branch protection information
     */
    public function update($username, $repository, $branch, array $params = [])
    {
        // Preview endpoint
        $this->acceptHeaderValue = 'application/vnd.github.luke-cage-preview+json';

        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection', $params);
    }

    /**
     * Remove the repo's branch protection.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-branch-protection
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     */
    public function remove($username, $repository, $branch)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection');
    }

    /**
     * Get required status checks of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#get-required-status-checks-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The required status checks information
     */
    public function showStatusChecks($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks');
    }

    /**
     * Update required status checks of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#update-required-status-checks-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch status checks information
     *
     * @return array The updated branch status checks information
     */
    public function updateStatusChecks($username, $repository, $branch, array $params = [])
    {
        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks', $params);
    }

    /**
     * Remove required status checks of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-required-status-checks-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     */
    public function removeStatusChecks($username, $repository, $branch)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks');
    }

    /**
     * List required status checks contexts of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#list-required-status-checks-contexts-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The required status checks contexts information
     */
    public function showStatusChecksContexts($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks/contexts');
    }

    /**
     * Replace required status checks contexts of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#replace-required-status-checks-contexts-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch status checks contexts information
     *
     * @return array The new branch status checks contexts information
     */
    public function replaceStatusChecksContexts($username, $repository, $branch, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks/contexts', $params);
    }

    /**
     * Add required status checks contexts of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#add-required-status-checks-contexts-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch status checks contexts information
     *
     * @return array The updated branch status checks contexts information
     */
    public function addStatusChecksContexts($username, $repository, $branch, array $params = [])
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks/contexts', $params);
    }

    /**
     * Remove required status checks contexts of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-required-status-checks-contexts-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch status checks contexts information
     *
     * @return array The updated branch status checks contexts information
     */
    public function removeStatusChecksContexts($username, $repository, $branch, array $params = [])
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_status_checks/contexts', $params);
    }

    /**
     * Get pull request review enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#get-pull-request-review-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The pull request review enforcement information
     */
    public function showPullRequestReviewEnforcement($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_pull_request_reviews');
    }

    /**
     * Update pull request review enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#update-pull-request-review-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The branch status checks information
     *
     * @return array The updated branch status checks information
     */
    public function updatePullRequestReviewEnforcement($username, $repository, $branch, array $params = [])
    {
        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_pull_request_reviews', $params);
    }

    /**
     * Remove pull request review enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-pull-request-review-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     */
    public function removePullRequestReviewEnforcement($username, $repository, $branch)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/required_pull_request_reviews');
    }

    /**
     * Get admin enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#get-admin-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The admin enforcement information
     */
    public function showAdminEnforcement($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/enforce_admins');
    }

    /**
     * Add admin enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#add-admin-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The updated admin enforcement information
     */
    public function addAdminEnforcement($username, $repository, $branch)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/enforce_admins');
    }

    /**
     * Remove admin enforcement of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-admin-enforcement-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     */
    public function removeAdminEnforcement($username, $repository, $branch)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/enforce_admins');
    }

    /**
     * Get restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#get-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The branch restrictions information
     */
    public function showRestrictions($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions');
    }

    /**
     * Remove restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     */
    public function removeRestrictions($username, $repository, $branch)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions');
    }

    /**
     * List team restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#list-team-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The branch team restrictions information
     */
    public function showTeamRestrictions($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/teams');
    }

    /**
     * Replace team restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#replace-team-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of team slugs with push access
     *
     * @return array The new branch team restrictions information
     */
    public function replaceTeamRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/teams', $params);
    }

    /**
     * Add team restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#add-team-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of team slugs with push access
     *
     * @return array The branch team restrictions information
     */
    public function addTeamRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/teams', $params);
    }

    /**
     * Remove team restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-team-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of team slugs with push access
     *
     * @return array The updated branch team restrictions information
     */
    public function removeTeamRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/teams', $params);
    }

    /**
     * List user restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#list-user-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     *
     * @return array The branch user restrictions information
     */
    public function showUserRestrictions($username, $repository, $branch)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/users');
    }

    /**
     * Replace user restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#replace-user-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of user logins with push access
     *
     * @return array The new branch user restrictions information
     */
    public function replaceUserRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/users', $params);
    }

    /**
     * Add user restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#add-user-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of user logins with push access
     *
     * @return array The branch user restrictions information
     */
    public function addUserRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/users', $params);
    }

    /**
     * Remove user restrictions of protected branch.
     *
     * @link https://developer.github.com/v3/repos/branches/#remove-user-restrictions-of-protected-branch
     *
     * @param string $username   The user who owns the repository
     * @param string $repository The name of the repo
     * @param string $branch     The name of the branch
     * @param array  $params     The list of user logins with push access
     *
     * @return array The updated branch user restrictions information
     */
    public function removeUserRestrictions($username, $repository, $branch, array $params = [])
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/branches/'.rawurlencode($branch).'/protection/restrictions/users', $params);
    }
}
