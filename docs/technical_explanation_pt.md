# EpsteinIslandEscapers - Documentação Técnica

## 1. Visão Geral do Projeto
EpsteinIslandEscapers é um jogo de escape room para web baseado em PHP com:
- Criação de equipes e rastreamento por sessão
- Três salas de jogo com progressão de puzzles
- Sistema de reviews (jogador + admin)
- Painel administrativo com autenticação e operações no estilo CRUD
- Persistência em MySQL usando PDO

Stack principal:
- Backend: PHP (estilo procedural com funções auxiliares)
- Banco de dados: MySQL via PDO
- Frontend: HTML, CSS, JavaScript (vanilla)

## 2. Arquitetura de Execução
### 2.1 Fluxo de Requisição
1. O navegador solicita uma rota PHP (por exemplo `index.php` ou `rooms/room_2.php`).
2. Os includes compartilhados carregam a estrutura da página (`includes/header.php`, `includes/nav.php`, `includes/footer.php`).
3. O bootstrap do banco é carregado a partir de `database.php` e o helper de schema a partir de `includes/schema.php`.
4. A lógica de negócio é executada (criação de equipe, checagem de puzzles, operações de review, telas admin).
5. A saída é renderizada no servidor.

### 2.2 Modelo de Estado
- A sessão (`$_SESSION`) armazena a identidade da equipe ativa e a progressão do jogo.
- O banco de dados armazena as entidades persistentes: equipes, perguntas e reviews.

## 3. Camada de Banco de Dados
### 3.1 Conexão
Arquivo: `database.php`
- Cria o banco se ele não existir (`epsteinislandescapers`)
- Abre PDO com UTF-8 (`utf8mb4`)
- Ativa exceptions com `PDO::ATTR_ERRMODE`

### 3.2 Inicialização do Schema
Arquivo: `includes/schema.php`
- Função `ensureProjectSchema(?PDO $db_connection): void`
- Garante que as tabelas existam:
  - `teams`
  - `question`
  - `reviews`
- Faz seed e migração dos registros de puzzles (incluindo normalização e tradução)

### 3.3 Uso do PDO
As operações de leitura e escrita no banco são implementadas com PDO em toda a aplicação:
- `prepare(...)` + `execute(...)` para writes/reads parametrizados
- `query(...)` para listagens diretas onde não há injeção de entrada do usuário

## 4. Entidades de Domínio
### 4.1 Equipe
Tabela: `teams`
- Campos principais: `team_name`, `member1..member4`
- Campos de rastreamento: `score`, `created_at`, `finished_at`, `elapsed_seconds`

### 4.2 Puzzle
Tabela: `question`
- `riddle`, `answer`, `hint`, `roomId`

### 4.3 Review
Tabela: `reviews`
- `team_id`, `rating`, `difficulty`, `feedback`, `created_at`
- `team_id` FK referencia `teams.id` (`ON DELETE SET NULL`)

## 5. Módulos de Gameplay
### 5.1 Criação de Equipe
Arquivo: `create_team.php`
- Valida os campos mínimos obrigatórios
- Insere o registro da equipe
- Salva o contexto da equipe na sessão (`team_id`, `team_name`)
- Retorna JSON para o fluxo AJAX do modal

### 5.2 Sala 1
Arquivos:
- `rooms/room_1.php`
- `js/room1-scene.js`
- `rooms/complete_room1.php`

Objetivo:
- Controla a primeira fase do gameplay e a progressão para as próximas salas

### 5.3 Sala 2
Arquivo: `rooms/room_2.php`
- Carrega os puzzles da sala a partir do banco (`roomId = 2`)
- Aplica helpers de normalização de resposta
- Exclui o riddle meta do teste de saída da lista de lockers

### 5.4 Sala 3
Arquivo: `rooms/room_3.php`
- Carrega os 3 riddles mais recentes da sala 3 a partir do banco (`roomId = 3`)
- Valida as respostas e acompanha o índice atual na sessão
- Chama `finalizeTeamEscape(...)` ao concluir
- Persiste `finished_at` e `elapsed_seconds`

## 6. Sistema de Reviews
### 6.1 Envio Público de Review
Arquivos:
- `submit_review.php`
- `reviews.php`

Comportamento:
- Exige a conclusão do jogo antes de permitir uma review
- Aceita rating/difficulty/feedback
- Salva a review com equipe vinculada quando existir

### 6.2 Interface de Reviews
Arquivos:
- `includes/footer.php`
- `js/review-modal.js`

Comportamento:
- Mostra reviews recentes em um modal
- Trata submissão AJAX e estados de resposta

## 7. Painel Admin
### 7.1 Autenticação
Arquivos:
- `admin/auth.php`
- `admin/login.php`
- `admin/logout.php`

Comportamento:
- Proteção de login baseada em sessão (`adminRequireLogin()`)
- Redireciona para o login quando o usuário não está autenticado

### 7.2 Admin de Equipes
Arquivos:
- `admin/add_team.php`
- `admin/show_all_teams.php`

Comportamento:
- Cria registros de equipe
- Lê visão completa incluindo score, tempo decorrido, quantidade de reviews e média de rating

### 7.3 Admin de Riddles
Arquivos:
- `admin/add_riddle.php`
- `admin/show_all_riddles.php`

Comportamento:
- Cria puzzles com answer/hint
- Lista todos os puzzles
- Remove registros de puzzle

### 7.4 Admin de Reviews
Arquivos:
- `admin/add_review.php`
- `admin/show_all_reviews.php`

Comportamento:
- Cria reviews manualmente
- Lista todas as reviews
- Remove registros de review

## 8. Componentes de UI Compartilhados
- `includes/header.php`: recursos globais do `<head>`
- `includes/nav.php`: navegação superior + badge da equipe
- `includes/footer.php`: footer + modais de create-team/review + ligação com JS
- `css/style.css`: tema principal e estilo do admin

## 9. Módulos JavaScript
- `js/menu.js`: comportamento do menu burger/lateral
- `js/join-riddle.js`: navegação suave até a seção do culto e fluxo de desbloqueio do culto
- `js/create-team-modal.js`: modal open/close/toggle + criação de equipe via AJAX
- `js/review-modal.js`: ciclo de vida do modal + envio de review via AJAX
- `js/room1-scene.js`: lógica da cena de puzzles da sala 1

## 10. Notas de Segurança e Integridade de Dados
- O risco de SQL injection foi reduzido usando PDO prepared statements nos fluxos de escrita
- As rotas admin estão protegidas por login
- A sessão carrega o estado do jogo e a identidade da equipe
- Possíveis melhorias futuras:
  - tokens CSRF para ações destrutivas do admin
  - hash de senha / credenciais em variáveis de ambiente

## 11. Organização por Pastas
A estrutura atual está separada por responsabilidade:
- `admin/` para endpoints/views do admin
- `rooms/` para gameplay
- `includes/` para partials/helpers compartilhados
- `js/` para módulos JavaScript
- `css/` para estilos
- `assets/` para mídia/fontes estáticas
- `sql/` para seed/referência SQL

## 12. Mapa Completo de Arquivos (Projeto Atual)
### Root
- `index.php`
- `create_team.php`
- `database.php`
- `reviews.php`
- `submit_review.php`
- `unlock_cult.php`
- `README.md`

### Admin
- `admin/index.php`
- `admin/auth.php`
- `admin/login.php`
- `admin/logout.php`
- `admin/add_team.php`
- `admin/show_all_teams.php`
- `admin/add_riddle.php`
- `admin/show_all_riddles.php`
- `admin/add_review.php`
- `admin/show_all_reviews.php`

### Includes
- `includes/header.php`
- `includes/nav.php`
- `includes/footer.php`
- `includes/schema.php`

### Rooms
- `rooms/room_1.php`
- `rooms/room_2.php`
- `rooms/room_3.php`
- `rooms/complete_room1.php`

### Assets de Frontend
- `css/style.css`
- `js/create-team-modal.js`
- `js/join-riddle.js`
- `js/menu.js`
- `js/review-modal.js`
- `js/room1-scene.js`

### Outros
- `sql/riddles.sql`
- `assets/*` (imagens, ícones, fontes)

## 13. Limpeza Realizada
A base de código foi limpa para remover arquivos que não eram mais referenciados pelo site em execução:
- arquivo legado exportado de dados de puzzle removido
- páginas antigas standalone de vitória/derrota removidas
- arquivos JavaScript sem referência removidos
- dependência externa de fallback da Room 2 substituída por um array inline

## 14. Resumo Técnico Final
A base de código entrega uma escape room web totalmente jogável com dados persistentes de equipe, gerenciamento de puzzles, gerenciamento de reviews e supervisão admin, organizada em pastas dedicadas e baseada em integração MySQL com PDO.
