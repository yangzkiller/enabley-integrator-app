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
    protected $description = 'Insere no grupo fixo da Enabley os colaboradores que ainda não foram sincronizados.';

    private string $groupId;

    public function __construct()
    {
        parent::__construct();
        $this->groupId = config('services.enabley.group_id');
    }

    public function handle()
    {
        $this->info('Iniciando sync:enabley...');

        $pending = DB::table('colaboradores')
            ->whereNull('enabley_identifier')
            ->get();

        $this->info("Colaboradores pendentes: {$pending->count()}");

        if ($pending->isEmpty()) {
            $this->info('Nenhum colaborador pendente.');
            return;
        }

        $enabley  = new EnableyService();
        $success  = 0;
        $skipped  = 0;
        $failed   = 0;

        foreach ($pending as $colaborador) {
            try {
                $identifier = $enabley->findIdentifierByCpf($colaborador->nr_cpf);

                if ($identifier) {
                    // Já existe na Enabley — só salva identifier localmente
                    DB::table('colaboradores')
                        ->where('id_colaborador', $colaborador->id_colaborador)
                        ->update(['enabley_identifier' => $identifier]);

                    $skipped++;
                    $this->line("⏭ já existe: {$colaborador->nm_colaborador} ({$colaborador->nr_cpf})");
                    continue;
                }

                // Não existe — cria na Enabley e adiciona ao grupo
                $identifier = (string) Str::uuid();

                $parts     = explode(' ', trim($colaborador->nm_colaborador), 2);
                $firstName = $parts[0];
                $lastName  = $parts[1] ?? '';

                $enabley->upsertUser(
                    identifier: $identifier,
                    firstName:  $firstName,
                    lastName:   $lastName,
                    cpf:        $colaborador->nr_cpf,
                );

                $enabley->addUserToGroup($this->groupId, $identifier);

                DB::table('colaboradores')
                    ->where('id_colaborador', $colaborador->id_colaborador)
                    ->update([
                        'enabley_identifier'   => $identifier,
                        'synced_to_enabley_at' => now(),
                    ]);

                $success++;
                $this->info("✓ {$colaborador->nm_colaborador} ({$colaborador->nr_cpf})");

            } catch (\Exception $e) {
                $failed++;
                Log::error("Erro ao sincronizar {$colaborador->nr_cpf}: {$e->getMessage()}");
                $this->error("✗ {$colaborador->nr_cpf}: {$e->getMessage()}");
            }
        }

        $this->info("Concluído — Criados: {$success} | Já existiam: {$skipped} | Falha: {$failed}");
    }
}