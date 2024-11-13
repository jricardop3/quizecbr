# Sistema de Jogos de Quiz

## Visão Geral
Este projeto é um sistema de jogos de quiz onde administradores podem criar e gerenciar quizzes com perguntas do tipo verdadeiro ou falso, e os usuários podem participar desses quizzes, acumular pontos e visualizar um ranking.

## Desenvolvimento API: 

### 1. Instalação de dependencias
- [X] **Laravel Sanctum**: para autenticação de usuários e proteção de rotas de API, proporcionando segurança e gerenciamento de tokens.
- Correção na configuração do Sanctum para autenticação de usuários (cors, auth e kernel).
- [X] **L5 Swagger**: para documentação automática da API, permitindo visualizar e testar os endpoints durante o desenvolvimento.
        - Configuração concluída. Para gerar a documentação, use o comando:`php artisan l5-swagger:generate`
        - A documentação estará disponível no navegador em `localhost/api/documentation`.
                -Docomentados até o momento: Auth, User, Quiz, Question e ParticipationControllers.
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
                        -ParticipationFactory: Configurado para criar perguntas associadas a user e um quiz, com score aleatório e uma data aleatória de uma semana até o momento

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
- [X] **QuestionController**
        -Controlador responsável pelo gerenciamento das participações dos usuários em quizzes.
        -Lógica para registrar as respostas dos usuários às perguntas de quizzes e armazenar as informações de participação no banco de dados.
        -Implementação de funcionalidades para salvar o progresso do usuário, permitindo que ele avance nas perguntas sem perder os dados das respostas já fornecidas.
        -Validações adequadas para garantir que as respostas sejam registradas corretamente, com retorno de mensagens claras em caso de erro.
        -Rotas configuradas para participar de quizzes, incluindo os métodos store, update e show para gerenciar o registro e atualização de participações.

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
        - **ParticipationTest**: cobre todos os métodos de QuestionController, incluindo:
                -Testando o índice de participações:
                        -Validação do retorno da listagem de participações de um quiz, com a verificação de que a estrutura JSON da resposta está correta, incluindo informações sobre a participação, usuário e quiz.
                        -Teste de cenário onde não há participações registradas para um quiz, garantindo que o retorno seja adequado (status 404 e mensagem "Nenhuma participação encontrada").
                -Testando a criação de participação:
                        -Teste bem-sucedido de criação de participação, onde o usuário envia as respostas para um quiz, e a participação é registrada com sucesso no banco de dados.
                        -Teste de cenário onde o quiz não é encontrado, garantindo que o erro seja tratado corretamente (status 404 e mensagem "Quiz não encontrado").
                        -Validação de respostas inválidas, como a tentativa de enviar uma pergunta inexistente, verificando que os erros de validação são retornados de forma clara.
                -Testando visualização de participação:
                        -Teste de tentativa de visualizar uma participação inexistente, com retorno adequado (status 404 e mensagem "Participação não encontrada").
                -Testando participação múltipla:
                        -Validação de que um usuário não pode participar de um mesmo quiz mais de uma vez, retornando um erro adequado (status 400 e mensagem "Você já participou deste quiz").

- [X] **IsAdmin**: Criação e implementação do middleware 'IsAdmin' para proteger rotas sensíveis, como o CRUD de usuários (exceto a rota de criação).

## Desenvolvimento Front-End:

### 1. Instalação de dependências
- [X] **Vue.js**: `npm install vue@3`
  - Configuração do `App.js` para montar o componente `App.vue` dentro do arquivo `Welcome.blade.php` (padrão do Laravel).
- [X] **vue-router**: `npm install vue-router@4 --legacy-peer-deps`
  - Criação e configuração do arquivo `router.js` para gerenciar as rotas da aplicação.
- [X] **Bootstrap**: 
  - `link cdn https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
  - `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js`

### 2. Views e Components
- [X] **App.vue**: Componente base para montar todas as views e componentes da aplicação, incluindo um menu de navegação acessível em todas as views.
- [X] **HomeView.vue**: Página inicial da aplicação.
- [X] **RegisterUserView.vue**: Página de registro de usuários.
- [X] **LoginUserView.vue**: Página de login de usuários.
- [X] **LoginAdminView.vue**: Página de login do administrador.
- [X] **QuizView.vue**: Página de exibição dos quizzes cadastrados.
- [X] **AnswerQuizView.vue**: Página de exibição das perguntas do quiz selecionado.
- [X] **DashboardAdminView.vue**: Painel de administração com visualização e gerenciamento de quizzes e perguntas.
- [X] **QuizCreateView.vue**: Página para criação de quizzes.
- [X] **QuizEditView.vue**: Página para edição de quizzes.
- [X] **QuestionCreateView.vue**: Página para criação de perguntas.
- [X] **QuestionEditView.vue**: Página para edição de perguntas.

### 3. Lógica e Funcionalidades
- [X] **CRUD completo**: Implementação da lógica de criação, leitura, atualização e exclusão (CRUD) para quizzes e perguntas.
- [X] **Rotas protegidas**: Implementação de rotas protegidas para acesso de administrador (login via token de autenticação).
- [X] **Exclusão de quizzes e perguntas**: Funcionalidade de exclusão de quizzes e perguntas com confirmação de ação.
- [X] **Menu de navegação**: Implementação de um menu fixo, incluindo um toggle dropdown para dispositivos móveis e links diretos para quizzes e páginas de edição.
- [X] **Submenu interno**: Criação de um componente `NavDashboard` com submenu para navegação interna no dashboard de administração.

## Dockerização:

### 1. **Dockerfile**
- [X] Criado um `Dockerfile` para configurar o ambiente, incluindo PHP, Composer, Node.js e Nginx.
- [X] Configuração de permissões para garantir que o Laravel consiga escrever nos diretórios..

### 2. **docker-compose.yml**
- [X] Configurado um arquivo `docker-compose.yml` para orquestrar os containers PHP, MySQL e Nginx.
- [X] Definidos volumes para persistência de dados do MySQL e refletir alterações no código sem reiniciar os containers.

### 3. **Banco de Dados MySQL**
- [X] Configurado MySQL com um banco de dados `quizecbr`, usuário e senha via variáveis de ambiente no `docker-compose.yml`.

### 4. **Comandos para Build e Execução**
- [X] Rodamos `docker build -t quizecbr .` para criar a imagem Docker.
- [X] Inicializamos os containers com `docker-compose up -d`.

### 5. **Execução da Aplicação**
- [X] A aplicação foi acessada via `http://localhost:8080`, com todos os containers comunicando-se corretamente.

### 6. **Finalização**
- [X] Executado `npm run build` para compilar o Vue.js em arquivos otimizados para produção, finalizando a dockerização do projeto.
- [X] Executado `php artisan migrate` Testar o banco de dados do container.
- [X] Executado `php artisan test` Todos os testes passaram com sucesso.
- [X] Executado `php artisan db:seed` Gerando o usuário admin para testes manuais da aplicação.

# Manual de Instalação - QuizECBR

Este manual contém todas as etapas necessárias para instalar e configurar a aplicação **QuizECBR** no seu ambiente local ou servidor. A aplicação utiliza **Laravel** para o backend e **Vue.js** para o frontend, além de ser containerizada utilizando **Docker**.

## Requisitos

-Antes de começar, verifique se você tem os seguintes requisitos no seu ambiente de desenvolvimento:

- **Docker** e **Docker Compose** instalados
- **Node.js** (recomendado v14 ou superior) – Para compilar o frontend com npm
- **PHP 8.1 ou superior**
- **Composer** (para gerenciamento de dependências PHP)
- **MySQL** (versão 5.7 ou superior)
- **Git** (opcional para clonar o repositório)

## Etapas de Instalação

### 1. Clonar o Repositório (se necessário)

-Caso não tenha o projeto em sua máquina, clone o repositório:

```bash
git clone https://github.com/seu-usuario/quizecbr.git
cd quizecbr
mv .env.example .env
composer install
```

### 2. Construir o Container com Docker
-Na raiz do projeto, execute o seguinte comando para construir os containers e rodar a aplicação:
```bash
docker-compose up -d --build
```
-Este comando irá construir os containers necessários, instalar as dependências PHP via Composer, compilar o frontend com npm e iniciar os containers em segundo plano.

### 3. Rodar as Migrações e Seeders
-Rodar as migrações para criar as tabelas no banco de dados:
```bash
docker-compose exec app php artisan migrate
```
-Rodar o seeder para criar o usuário administrador:
```bash
docker-compose exec app php artisan db:seed
```
Acessos de administrador: email: admin@example.com e senha: password.
-Criar o link simbólico para o armazenamento de arquivos (para upload de imagens):
```bash
docker-compose exec app php artisan storage:link
```
### 4. Integridade da aplicação
-Verifique a integridade da aplicação rodando os testes de integração:
```bash
docker-compose exec app php artisan test
```
-Verifique conformidade com PSR12:
```bash
php vendor/bin/phpcs --standard=PSR12 app/
```
-Verifique ajustar apontamentos PSR12:
```bash
php vendor/bin/phpcbf --standard=PSR12 app/
```
Este comando irá executar todos os testes da aplicação, garantindo que todos os fluxos funcionais estejam funcionando corretamente.
### 5. Acessar a Aplicação
-Após a construção dos containers e execução dos testes, você pode acessar a aplicação através de seu navegador em:
```bash
http://localhost:8080
```
-Documentação da API:
```bash
http://localhost:8080/api/documentation
```
## Finalizando


### Com a aplicação configurada, você pode acessar as funcionalidades do sistema de quizzes, fazer login como administrador ou usuário e começar a interagir com a plataforma.

