<?php

declare(strict_types=1);

namespace Github\Api\App;

use Github\Api\AbstractApi;

class Hook extends AbstractApi
{
    /**
     * Show the app hook configuration.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks#get-a-webhook-configuration-for-an-app
     *
     * @return array
     */
    public function showConfig()
    {
        return $this->get('/app/hook/config');
    }

    /**
     * Update the hook configuration of an app.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks#update-a-webhook-configuration-for-an-app
     *
     * @param array $params
     *
     * @return array
     */
    public function updateConfig(array $params)
    {
        return $this->patch('/app/hook/config', $params);
    }

    /**
     * List deliveries for an app webhook.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks#list-deliveries-for-an-app-webhook
     *
     * @return array
     */
    public function deliveries()
    {
        return $this->get('/app/hook/deliveries');
    }

    /**
     * Get a delivery for an app webhook.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks#get-a-delivery-for-an-app-webhook
     *
     * @param int $delivery
     *
     * @return array
     */
    public function delivery($delivery)
    {
        return $this->get('/app/hook/deliveries/'.$delivery);
    }

    /**
     * Redeliver a delivery for an app webhook.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks#redeliver-a-delivery-for-an-app-webhook
     *
     * @param int $delivery
     *
     * @return array
     */
    public function redeliver($delivery)
    {
        return $this->post('/app/hook/deliveries/'.$delivery.'/attempts');
    }
}
