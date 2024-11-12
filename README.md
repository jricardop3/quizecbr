# Sistema de Jogos de Quiz

## Visão Geral
Este projeto é um sistema de jogos de quiz onde administradores podem criar e gerenciar quizzes com perguntas do tipo verdadeiro ou falso, e os usuários podem participar desses quizzes, acumular pontos e visualizar um ranking.

## Desenvolvimento:

### 1. Instalação de dependencias
- [X] **Laravel Sanctum**: para autenticação de usuários e proteção de rotas de API, proporcionando segurança e gerenciamento de tokens.
- [X] **L5 Swagger**: para documentação automática da API, permitindo visualizar e testar os endpoints durante o desenvolvimento.

### 2. Banco de dados
- [X] **Migrations** - Criação de migrations Quizzes, Questions, User_ansuers, User_scores, Participations (add de role a users)
- [X] **Models** - Criação de Models: Quiz, Question, User_ansuer, User_score, Participation (add role em user) e seus relacionamentos.
                    - Quiz possui muitas Questions, Participations e UserScores.
                    - Question pertence a um Quiz e possui muitos UserAnswers.
                    - UserAnswer pertence a User e Question.
                    - UserScore pertence a User e Quiz.
                    - Participation pertence a User e Quiz.
                    - User possui muitos UserScores, UserAnswers e Participations.
- [X] **Seeder** - Criação do AdminUserSeeder para geração de um usuário administrador padrão (role 'admin') para acesso inicial ao sistema.

### 3. Controllers e Testes de integração
- [X] **AuthController** 
- [X] **UserController** 
        -Implementação dos controladores AuthController e UserController com autenticação, gerenciamento de usuários e autorização básica de administrador.
        -Rotas configuradas para Auth (login, logout) e User (index, show, store, update, destroy).
- [X] **Testes**: -Testes de Integração adicionados:
        -AuthControllerTest: cobre login e logout para usuários e administradores.
        -UserControllerTest: cobre listagem, criação, exibição, atualização e exclusão de usuários.
- [X] **Factory**: -Configuração do UserFactory para incluir a criação de usuários com senha e função (user), o que auxilia nos testes de integração.


