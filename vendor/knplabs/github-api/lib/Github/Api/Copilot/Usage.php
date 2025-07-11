<?php

namespace Github\Api\Copilot;

use Github\Api\AbstractApi;

class Usage extends AbstractApi
{
    public function orgUsageSummary(string $organization, array $params = []): array
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/copilot/usage', $params);
    }

    public function orgTeamUsageSummary(string $organization, string $teamSlug, array $params = []): array
    {
        return $this->get(
            '/orgs/'.rawurlencode($organization).'/team/'.rawurlencode($teamSlug).'/copilot/usage',
            $params
        );
    }

    public function enterpriseUsageSummary(string $enterprise, array $params = []): array
    {
        return $this->get('/enterprises/'.rawurlencode($enterprise).'/copilot/usage', $params);
    }

    public function enterpriseTeamUsageSummary(string $enterprise, string $teamSlug, array $params = []): array
    {
        return $this->get(
            '/enterprises/'.rawurlencode($enterprise).'/team/'.rawurlencode($teamSlug).'/copilot/usage',
            $params
        );
    }
}
