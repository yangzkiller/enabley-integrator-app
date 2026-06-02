<?php

namespace App\Console\Commands;

use App\Services\EnableyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncEnabley extends Command
{
    protected $signature = 'sync:enabley';
    protected $description = 'Insere no grupo fixo da Enabley apenas os colaboradores que ainda não possuem identificador.';

    private string $groupId;

    public function __construct()
    {
        parent::__construct();
        $this->groupId = config('services.enabley.group_id');
    }

    public function handle()
    {
        $this->info('Iniciando sync:enabley...');

        // Mudança aqui: Buscamos APENAS quem não tem o identificador da Enabley
        $pending = DB::table('colaboradores')
            ->whereNull('enabley_identifier')
            ->get();

        $this->info("Colaboradores pendentes de sincronização: {$pending->count()}");

        if ($pending->isEmpty()) {
            $this->info('Nenhum colaborador pendente.');
            return;
        }

        $enabley = new EnableyService();
        $success = 0;
        $failed  = 0;

        foreach ($pending as $colaborador) {
            try {
                // 1. Validamos na API se ele realmente não existe por CPF (Garantia dupla)
                $identifier = $enabley->findIdentifierByCpf($colaborador->nr_cpf);

                if ($identifier) {
                    // Se o CPF já existe na Enabley, apenas atualizamos o nosso banco para evitar futuras consultas
                    $this->line("⏭️ Já existe na Enabley, vinculando identificador local: {$colaborador->nm_colaborador}");
                    
                    DB::table('colaboradores')
                        ->where('id_colaborador', $colaborador->id_colaborador)
                        ->update([
                            'enabley_identifier'   => $identifier,
                            'synced_to_enabley_at' => now(),
                        ]);
                        
                    $success++;
                    continue;
                }

                // 2. Se realmente não existe, cria um novo UUID
                $identifier = (string) Str::uuid();

                $parts     = explode(' ', trim($colaborador->nm_colaborador), 2);
                $firstName = $parts[0];
                $lastName  = $parts[1] ?? '';

                // Cria o usuário na Enabley
                $enabley->upsertUser(
                    identifier: $identifier,
                    firstName:  $firstName,
                    lastName:   $lastName,
                    cpf:        $colaborador->nr_cpf,
                );

                // Adiciona ao novo grupo fixo
                $enabley->addUserToGroup($this->groupId, $identifier);

                // Atualiza o banco local
                DB::table('colaboradores')
                    ->where('id_colaborador', $colaborador->id_colaborador)
                    ->update([
                        'enabley_identifier'   => $identifier,
                        'synced_to_enabley_at' => now(),
                    ]);

                $success++;
                $this->info("✓ Novo usuário inserido no grupo: {$colaborador->nm_colaborador} ({$colaborador->nr_cpf})");

            } catch (\Exception $e) {
                $failed++;
                Log::error("Erro ao sincronizar {$colaborador->nr_cpf}: {$e->getMessage()}");
                $this->error("✗ {$colaborador->nr_cpf}: {$e->getMessage()}");
            }
        }

        $this->info("Concluído — Sucesso: {$success} | Falha: {$failed}");
    }
}