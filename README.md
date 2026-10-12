# Finance Project

API para **organizar despesas em grupo e arrecadar dinheiro via Pix**, com cobranças lembradas automaticamente pelo WhatsApp.

O usuário cria **eventos** (uma viagem, uma festa, uma república), dentro deles **grupos de despesa** com participantes, e o sistema gera as cobranças — à vista, parceladas ou divididas entre todos. Um dia antes de cada vencimento, os participantes recebem um lembrete no WhatsApp. Também é possível criar **campanhas de arrecadação** (vaquinhas) com QR Code Pix próprio, recebendo o dinheiro direto numa subconta do usuário.

> Projeto de estudos, construído para praticar integrações com serviços externos, processamento assíncrono e comunicação em tempo real com Laravel.

## Funcionalidades

**Despesas em grupo**
- Eventos com vários grupos de despesa e participantes
- Cobranças à vista, parceladas ou divididas igualmente entre os participantes
- Recálculo automático das parcelas quando o valor muda ou um participante entra no grupo (bloqueado quando já existe parcela paga)
- Chat por grupo em tempo real

**Lembretes por WhatsApp**
- Conexão de um número de WhatsApp via QR Code, com status da conexão atualizado em tempo real
- Job diário que encontra parcelas vencendo no dia seguinte e envia uma mensagem para cada participante
- Notificação para o dono do grupo quando o WhatsApp do evento está desconectado

**Campanhas e pagamentos (Asaas)**
- Criação de subconta no Asaas para o usuário, com envio do link de verificação de documentos por e-mail
- Geração automática de chave Pix quando a subconta é aprovada
- Campanhas com QR Code Pix estático, meta opcional e data de encerramento
- Contribuições registradas por webhook, sem duplicar o mesmo pagamento
- Encerramento automático das campanhas na data de vencimento

**Dashboards**
- Despesas: valores pendentes e pagos no total, na semana, no mês e num período escolhido
- Campanhas: total arrecadado, progresso em relação à meta e quanto falta

**Conta e acesso**
- Autenticação com JWT e refresh token
- Recuperação de senha por e-mail
- Papéis e permissões por rota, além de policies por recurso (dono x participante)
- Endereço preenchido automaticamente pelo CEP (BrasilAPI)

## Stack

| | |
|---|---|
| **Linguagem / framework** | PHP 8.3, Laravel 12 |
| **Banco de dados** | PostgreSQL 17 |
| **Filas e cache** | Redis + Laravel Horizon |
| **Tempo real** | Laravel Reverb (WebSockets) |
| **Autenticação** | JWT (`tymon/jwt-auth`) |
| **Permissões** | `spatie/laravel-permission` |
| **Documentação da API** | Scramble (OpenAPI gerado a partir do código) |
| **Integrações** | Asaas (pagamentos/Pix), Evolution API (WhatsApp), BrasilAPI (CEP) |
| **Infra local** | Docker Compose |

## Como rodar

**Pré-requisitos:** Docker e Docker Compose.

```bash
git clone https://github.com/KauaHenrique06/finance-project.git
cd finance-project
cp .env.example .env
```

Além das variáveis padrão do Laravel, preencha no `.env`:

| Variável | Para quê |
|---|---|
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | PostgreSQL |
| `JWT_SECRET` | Gerado com `php artisan jwt:secret` |
| `ASAAS_URL_SANDBOX`, `ASAAS_API_KEY_SANDBOX`, `ASAAS_WALLET_ID_SANDBOX`, `ASAAS_WEBHOOK_TOKEN` | Pagamentos (conta sandbox do Asaas) |
| `EVOLUTION_API_URL`, `AUTHENTICATION_API_KEY`, `WEBHOOK_GLOBAL_URL` | WhatsApp (Evolution API) |
| `BRASIL_API_URL` | Consulta de CEP (`https://brasilapi.com.br/api`) |
| `REVERB_*` | WebSockets |
| `MAIL_*` | Envio de e-mails |

Suba os containers e prepare a aplicação:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

Para receber webhooks do Asaas e da Evolution API localmente, exponha a API com um túnel (ngrok, Cloudflare Tunnel) e use essa URL em `APP_URL` e `WEBHOOK_GLOBAL_URL`.

Para os jobs agendados rodarem, mantenha o scheduler ativo:

```bash
docker compose exec app php artisan schedule:work
```
