# Sistema de Jogos de Quiz

## Visão Geral
Este projeto é um sistema de jogos de quiz onde administradores podem criar e gerenciar quizzes com perguntas do tipo verdadeiro ou falso, e os usuários podem participar desses quizzes, acumular pontos e visualizar um ranking.

## Desenvolvimento:

### 1. Instalação de dependencias
- [X] **Laravel Sanctum**: para autenticação de usuários e proteção de rotas de API, proporcionando segurança e gerenciamento de tokens.
- Correção na configuração do Sanctum para autenticação de usuários (cors, auth e kernel).
- [X] **L5 Swagger**: para documentação automática da API, permitindo visualizar e testar os endpoints durante o desenvolvimento.
        - Configuração concluída. Para gerar a documentação, use o comando:`php artisan l5-swagger:generate`
        - A documentação estará disponível no navegador em `localhost/api/documentation`.
                -Docomentados até o momento: Auth, User, Quiz e QuestionsControllers.
- [X] **PHP CodeSniffer**: Instalado para manter um padrão de código consistente e legível.
        - Escanear o projeto para verificar conformidade com a PSR-12: `php vendor/bin/phpcs --standard=PSR12 app/`
        - Corrigir automaticamente com o PHP Code Beautifier and Fixer: `php vendor/bin/phpcbf --standard=PSR12 app/`


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
- [X] **Factory**: 
                        -Configuração do UserFactory para incluir a criação de usuários com senha e função (user), o que auxilia nos testes de integração.
                        -QuizFactory: Criado para gerar quizzes com título, descrição e imagem fake, possibilitando cenários variados em testes de integração.
                        -QuestionFactory: Configurado para criar perguntas associadas a um quiz, com texto e resposta correta (true ou false).
### 3. Controllers, Middlewares e Testes de Integração
- [X] **AuthController** 
- [X] **UserController** 
        - Implementação dos controladores AuthController e UserController com autenticação, gerenciamento de usuários e autorização básica de administrador.
        - Ajustes nos retornos e feedbacks dos endpoints de usuário para refletir mensagens de erro apropriadas em casos de validação, não autorização e erros internos.
        - Rotas configuradas para Auth (login, logout) e User (index, show, store, update, destroy).

- [X] **QuizController**
        - Controlador para gerenciar o CRUD de quizzes, incluindo funcionalidades para criar, listar, atualizar e excluir quizzes.
        - Implementação de lógica para upload e remoção de imagens associadas ao quiz.
        - Validações e tratamento de erros para fornecer feedback detalhado ao usuário em cada operação.
        - Rotas configuradas para quizzes (index, store, edit, update, delete)
        
- [X] **QuestionController**
        - Controlador para gerenciar o CRUD de perguntas, com suporte para associar perguntas a quizzes específicos.
        - Tratamento para uploads de imagens em perguntas e lógica de remoção de arquivos ao atualizar ou excluir.
        - Validações de dados e retornos apropriados em caso de erros, permitindo integração com o front-end com mensagens claras.
        - Rotas configuradas para question (index, store, edit, update, delete)

- [X] **Testes**: Testes de Integração adicionados:
        - **AuthControllerTest**: cobre login e logout para usuários e administradores.
        - **UserControllerTest**: cobre listagem, criação, exibição, atualização e exclusão de usuários.
                - Testes adicionais para validar o middleware 'IsAdmin', garantindo que:
                - Acesso negado para usuários sem permissão administrativa resulta no status 403 e mensagem de erro adequada.
                - Acesso concedido para usuários administradores.
        - **QuizControllerTest**: cobre todos os métodos de QuizController, validando:
                - Criação de quizzes com validação de dados e upload de imagens.
                - Listagem de quizzes, verificando resposta 404 quando vazia.
                - Edição e exclusão, testando atualização correta dos campos e remoção de imagem associada.
                - Mensagens de erro em caso de validações falhas e operações inválidas.
        - **QuestionControllerTest**: cobre todos os métodos de QuestionController, incluindo:
                - Criação e listagem de perguntas com validação completa de dados.
                - Atualização e exclusão de perguntas associadas a quizzes, verificando manutenção correta de relacionamentos e imagens.
                - Respostas de erro apropriadas para falhas de validação e operações inválidas.

- [X] **IsAdmin**: Criação e implementação do middleware 'IsAdmin' para proteger rotas sensíveis, como o CRUD de usuários (exceto a rota de criação).





