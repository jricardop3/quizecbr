# Sistema de Jogos de Quiz - DEV BACKEND PLENO

## Objetivo

Desenvolver um sistema de jogos de quiz onde um administrador pode criar e gerenciar quizes. Cada quiz terá perguntas com texto, imagem e respostas do tipo verdadeiro ou falso. Usuários que responderem ao quiz ganharão pontos, com possibilidade de visualizar um ranking.

## Requisitos do Projeto

### 1. Usuário Administrador
- Deve haver um usuário administrador responsável pelo gerenciamento dos quizes.
- Apenas o administrador poderá criar, editar e deletar quizes, perguntas e respostas.

### 2. Gerenciamento de Quizes
- Possibilidade de criar múltiplos quizes.
- Cada quiz deverá incluir:
    - **Texto da Pergunta:** Descrição ou pergunta para o jogador.
    - **Imagem:** Opcionalmente, uma imagem relacionada à pergunta.
    - **Respostas:** Opções de resposta “Verdadeiro” ou “Falso”.

### 3. Participação, Pontuação e Ranking
- Usuários não autenticados devem informar **nome** e **email** para se cadastrar e participar do quiz.
- Não são permitidos **emails repetidos** para participação no quiz, garantindo a unicidade de emails.
- Mensagens de erro claras devem ser retornadas em caso de falha no cadastro ou participação.
- Cada resposta correta vale **10 pontos**.
- O sistema deve manter um **ranking** dos jogadores com base nos pontos acumulados.

### 4. Autenticação
- Implementação de autenticação para que apenas o administrador possa gerenciar os quizes.

### 5. Tecnologias
- **PHP** com o framework **Laravel** será utilizado para o desenvolvimento do backend.
- A API deve seguir o padrão **REST** ou **RESTful** para uma estrutura consistente e clara.
- O sistema deve ser robusto e bem estruturado, com separação de responsabilidades e uso adequado de controllers, services, e repositórios, além de validação de dados.

### 6. Padronização e Docker
- **Padronização de Código:** Utilizar **PHP CodeSniffer** para manter o padrão de código consistente e legível (PSR-12).
- **Docker:** Disponibilizar o projeto com suporte a **Docker**, com um `Dockerfile` e `docker-compose.yml` para facilitar a configuração do ambiente de desenvolvimento.

### 7. Entrega
- O código deve ser enviado em um repositório no GitHub (ou GitLab) com um README explicando como rodar o projeto, as tecnologias utilizadas e outros detalhes relevantes.

## Diferenciais

- Implementação de uma interface **frontend** simples para consumir a API.
- Documentação da API usando Swagger ou similar.
- Testes unitários ou de integração para as principais funcionalidades.
- Uso de padrões de projeto, como Repository Pattern e Service Layer.

## Critérios de Avaliação

- Qualidade do código e organização do repositório.
- Cumprimento dos requisitos do projeto.
- Boas práticas de desenvolvimento (clean code, separação de responsabilidades).
- Uso adequado de tecnologias e soluções.

Estamos ansiosos para ver o desenvolvimento desta aplicação! Boa sorte e divirta-se desenvolvendo.
