# EpsteinIslandEscapers - Explicação Simples

## O que este site faz
Este é um site de escape room onde os jogadores:
1. Criam uma equipe
2. Jogam por 3 salas com puzzles
3. Escapam e recebem um tempo final
4. Deixam uma review

Também existe uma área admin para gerenciar equipes, riddles e reviews.

## Como o jogo funciona (simples)
### Passo 1: Criar uma equipe
- Uma equipe preenche um formulário (nome da equipe + membros).
- O site salva a equipe no banco de dados.
- O nome da equipe é armazenado na sessão e mostrado na navegação.

### Passo 2: Jogar Room 1, Room 2, Room 3
- Cada sala tem sua própria lógica de puzzles.
- As respostas são verificadas pelo site.
- O jogo mantém o progresso na sessão.

### Passo 3: Escapar e salvar o tempo
- Quando a equipe termina a Room 3, o site salva:
  - Horário final
  - Tempo total decorrido

### Passo 4: Review
- As equipes podem enviar uma review depois de terminar.
- As reviews são salvas e mostradas no site.

## O que o admin pode fazer
### Equipes
- Adicionar equipes
- Ver todas as equipes
- Ver score, tempo final e estatísticas de review

### Riddles
- Adicionar riddles, answers e hints
- Ver todos os riddles
- Deletar riddles

### Reviews
- Adicionar reviews
- Ver todas as reviews
- Deletar reviews

## Principais pastas (visão simples)
- `admin/` -> páginas admin
- `rooms/` -> salas do jogo
- `includes/` -> partes compartilhadas como header/nav/footer
- `js/` -> comportamento JavaScript
- `css/` -> estilo
- `assets/` -> imagens e fontes
- `sql/` -> arquivo SQL

## Banco de dados em palavras simples
O site usa MySQL com PDO. Principais tabelas:
- `teams` -> informações da equipe e tempo
- `question` -> riddles e answers
- `reviews` -> feedback dos jogadores

## Arquivos importantes (mapa rápido)
- `index.php` -> página inicial
- `create_team.php` -> endpoint de criação de equipe
- `database.php` -> conexão com o banco
- `rooms/room_1.php` -> room 1
- `rooms/room_2.php` -> room 2
- `rooms/room_3.php` -> room 3 + lógica de tempo final
- `submit_review.php` -> endpoint de envio de review
- `reviews.php` -> página pública de reviews

## Resumo curto de qualidade técnica
- Usa PDO para consultas no banco
- Tem estado do jogo baseado em sessão
- Usa pastas separadas por responsabilidade
- Tem páginas de autenticação admin

## Resumo em uma linha
É um projeto completo de escape room em PHP com gerenciamento de equipes, gameplay de puzzles, persistência de tempo, reviews e painel admin.
