<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnableyService
{
    private string $baseUrl;
    private string $clientKey;
    private string $secret;
    private ?string $token = null;

    public function __construct()
    {
        $this->baseUrl   = config('services.enabley.base_url');
        $this->clientKey = config('services.enabley.client_key');
        $this->secret    = config('services.enabley.secret');
    }

    // ─── Auth ────────────────────────────────────────────────────────────────

    private function authenticate(): void
    {
        $response = Http::post($this->baseUrl . '/api/v1/token?' . http_build_query([
            'clientKey' => $this->clientKey,
            'secret'    => $this->secret,
        ]));

        $this->token = $response->json('access_token');
    }

    private function token(): string
    {
        if (!$this->token) $this->authenticate();
        return $this->token;
    }

    // ─── Users ───────────────────────────────────────────────────────────────

    /**
     * Cria ou atualiza um usuário na Enabley.
     */
    public function upsertUser(
        string $identifier,
        string $firstName,
        string $lastName,
        string $cpf,
        string $subAccount = 'cejam'
    ): void {
        Http::withToken($this->token())->put($this->baseUrl . '/api/v2/users', [
            'identifier'     => $identifier,
            'firstName'      => $firstName,
            'lastName'       => $lastName,
            'username'       => $cpf,
            'possibleRoles'  => ['LEARNER'],
            'subAccountName' => $subAccount,
            'password'       => 'Cejam@2026',
            'isActive'       => true,
        ]);
    }

    /**
     * Busca o identifier de um usuário pelo CPF (username).
     */
    public function findIdentifierByCpf(string $cpf): ?string
    {
        $response = Http::withToken($this->token())
            ->get($this->baseUrl . '/api/v3/users', [
                'usernameSubstring' => $cpf,
            ]);

        return $response->json('items.0.identifier');
    }

    // ─── Groups ──────────────────────────────────────────────────────────────

    /**
     * Adiciona um usuário a um grupo.
     */
    public function addUserToGroup(string $groupIdentifier, string $userIdentifier): void
    {
        Http::withToken($this->token())
            ->withQueryParameters(['replace' => 'false'])
            ->post($this->baseUrl . "/api/v1/groups/{$groupIdentifier}/users/{$userIdentifier}");
    }

    /**
     * Busca um grupo pelo identificador.
     */
    public function getGroup(string $groupIdentifier): ?object
    {
        $response = Http::withToken($this->token())
            ->get($this->baseUrl . "/api/v1/groups/{$groupIdentifier}");

        return $response->ok() ? $response->object() : null;
    }

    /**
     * Conta o número de membros de um grupo.
     */
    public function countGroupMembers(string $groupIdentifier): int
    {
        $count = 0;
        $paginationKey = null;

        do {
            $params = ['subAccountName' => 'cejam'];
            if ($paginationKey) $params['paginationKey'] = $paginationKey;

            $response = Http::withToken($this->token())
                ->get($this->baseUrl . '/api/v3/users', $params);

            $data = $response->object();
            $users = $data->items ?? [];

            foreach ($users as $user) {
                // Verifica se o usuário está no grupo
                $groupsResponse = Http::withToken($this->token())
                    ->get($this->baseUrl . "/api/v1/users/{$user->identifier}/groups");
                
                $groups = $groupsResponse->json() ?? [];
                foreach ($groups as $group) {
                    if ($group['identifier'] === $groupIdentifier) {
                        $count++;
                        break;
                    }
                }
            }

            $paginationKey = $data->paginationKey ?? null;

        } while ($paginationKey);

        return $count;
    }
}