
<div align="center">

# 🛡️ Sistema de Gestão e Emissão de APR

### Plataforma de Gerenciamento de Análises Preliminares de Risco

**Organização de clientes, atividades, riscos, medidas de controle e executantes por meio de uma API REST segura.**

<p>
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"/>
  <img src="https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL"/>
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite"/>
  <img src="https://img.shields.io/badge/PHPUnit-366488?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit"/>
</p>

<p>
  <img src="https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow?style=flat-square" alt="Status"/>
  <img src="https://img.shields.io/badge/Testes-20%20Aprovados-brightgreen?style=flat-square" alt="Testes"/>
  <img src="https://img.shields.io/badge/Assertions-55-success?style=flat-square" alt="Assertions"/>
  <img src="https://img.shields.io/badge/Arquitetura-API%20REST-blue?style=flat-square" alt="API REST"/>
</p>

</div>

---

## 📖 Sobre o projeto

O **Sistema de Gestão e Emissão de APR** é uma aplicação voltada ao gerenciamento das informações utilizadas em uma **Análise Preliminar de Risco (APR)**.

O objetivo é organizar as informações de clientes, atividades, etapas operacionais, riscos envolvidos, medidas de controle e profissionais executantes.

Atualmente, o projeto conta com um **Back-End desenvolvido em Laravel**, utilizando autenticação, autorização por usuário, banco de dados relacional e testes automatizados.

A evolução do Front-End e a geração de documentos APR fazem parte das próximas etapas de desenvolvimento.

---

## ✨ Funcionalidades

- 👥 **Gestão de clientes:** cadastro, consulta, edição, listagem e exclusão.
- 📋 **Gestão de APRs:** criação, visualização, atualização e exclusão de análises.
- 🧩 **Etapas das atividades:** registro de etapas, riscos e medidas de controle.
- 👷 **Gestão de executantes:** identificação dos profissionais envolvidos nas atividades.
- 🔗 **Relacionamento entre APRs e clientes:** associação das análises aos clientes cadastrados.
- 🔐 **Autenticação:** proteção das rotas utilizando Laravel Sanctum.
- 🛡️ **Autorização:** isolamento dos registros entre diferentes usuários.
- ✅ **Validação de dados:** proteção contra associações indevidas entre APRs e clientes.
- 🔄 **Transações:** utilização de transações para manter a consistência das operações.

---

## 🛠️ Tecnologias utilizadas

<div align="center">

| Back-End | Banco de Dados | Ferramentas |
|:---:|:---:|:---:|
| <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" width="40" alt="PHP"/> | <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-original.svg" width="40" alt="PostgreSQL"/> | <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/git/git-original.svg" width="40" alt="Git"/> |
| **PHP** | **PostgreSQL** | **Git** |
| <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" width="40" alt="Laravel"/> | <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/sqlite/sqlite-original.svg" width="40" alt="SQLite"/> | <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/composer/composer-original.svg" width="40" alt="Composer"/> |
| **Laravel** | **SQLite (testes)** | **Composer** |

</div>

**Outras tecnologias:**

- Laravel Sanctum
- Eloquent ORM
- PHPUnit
- API REST
- Git e GitHub

---

## 🧱 Estrutura do projeto

```text
sistema-apr/
│
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AprController.php
│   │   │       └── ClienteController.php
│   │   │
│   │   └── Models/
│   │       ├── Apr.php
│   │       ├── Cliente.php
│   │       ├── EtapaApr.php
│   │       ├── ExecutanteApr.php
│   │       └── User.php
│   │
│   ├── database/
│   │   └── migrations/
│   │
│   ├── routes/
│   │
│   ├── tests/
│   │   └── Feature/
│   │       ├── AprAuthorizationTest.php
│   │       ├── ClienteAuthorizationTest.php
│   │       ├── DatabaseConfigurationTest.php
│   │       └── DatabaseMigrationTest.php
│   │
│   └── .env.example
│
└── README.md
```

---

## 🌐 Endpoints da API

As rotas de clientes e APRs são protegidas pelo middleware **`auth:sanctum`**.

### 👥 Clientes

| Método HTTP | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api/clientes` | Listar os próprios clientes |
| `POST` | `/api/clientes` | Cadastrar cliente |
| `GET` | `/api/clientes/{id}` | Consultar cliente |
| `PUT / PATCH` | `/api/clientes/{id}` | Atualizar cliente |
| `DELETE` | `/api/clientes/{id}` | Excluir cliente |

### 📋 APRs

| Método HTTP | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api/aprs` | Listar as próprias APRs |
| `POST` | `/api/aprs` | Cadastrar APR |
| `GET` | `/api/aprs/{id}` | Consultar APR |
| `PUT / PATCH` | `/api/aprs/{id}` | Atualizar APR |
| `DELETE` | `/api/aprs/{id}` | Excluir APR |

### 📡 Respostas HTTP

| Código | Significado |
|---|---|
| `200` | Operação realizada com sucesso |
| `201` | Recurso criado |
| `401` | Usuário não autenticado |
| `404` | Recurso não encontrado ou não autorizado por propriedade |
| `422` | Erro de validação |

---

## 🔒 Segurança e autorização

O sistema implementa mecanismos para garantir que cada usuário tenha acesso apenas às informações pertencentes à sua conta.

### Autenticação

As rotas protegidas utilizam o **Laravel Sanctum**.

### Isolamento de registros

Os relacionamentos do Eloquent permitem buscar registros associados ao usuário autenticado:

```php
$clientes = $request->user()
    ->clientes()
    ->get();
```

```php
$aprs = $request->user()
    ->aprs()
    ->get();
```

### Proteção contra acesso indevido

O sistema restringe:

- Consultas de clientes pertencentes a outros usuários.
- Atualizações e exclusões não autorizadas.
- Acesso a APRs de outras contas.
- Associação de APRs a clientes de outro usuário.
- Manipulação do proprietário durante o cadastro.

### Integridade dos dados

A tabela `clientes` possui uma chave estrangeira `user_id`, associada à tabela `users`.

Essa coluna utiliza a restrição **`NOT NULL`**, impedindo clientes sem proprietário.

As operações de criação e atualização das APRs utilizam transações para preservar a consistência dos dados relacionados.

---

## 🚀 Como executar o projeto

### Pré-requisitos

- PHP compatível com o Laravel utilizado.
- Composer.
- PostgreSQL.
- Extensão `pdo_pgsql` habilitada no PHP.

### 1. Acessar o Back-End

```bash
cd backend
```

### 2. Instalar as dependências

```bash
composer install
```

### 3. Configurar o ambiente

No PowerShell:

```powershell
Copy-Item .env.example .env
```

Configure o banco de dados no arquivo `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_do_banco
DB_PASSWORD=senha_local
```

### 4. Gerar a chave da aplicação

```bash
php artisan key:generate
```

### 5. Executar as migrations

```bash
php artisan migrate
```

> [!WARNING]
> Evite executar `php artisan migrate:fresh` em bancos com dados importantes, pois esse comando recria as tabelas e exclui seus registros.

### 6. Iniciar o servidor

```bash
php artisan serve
```

Endereço padrão:

```text
http://127.0.0.1:8000
```

---

## 🧪 Testes automatizados

O projeto utiliza **PHPUnit** para validar o funcionamento da aplicação.

O ambiente de testes está configurado para utilizar **SQLite em memória**, mantendo os testes isolados do PostgreSQL da aplicação.

### Executar todos os testes

```bash
php artisan test
```

### Testar autorização das APRs

```bash
php artisan test tests/Feature/AprAuthorizationTest.php
```

### Testar autorização dos clientes

```bash
php artisan test tests/Feature/ClienteAuthorizationTest.php
```

### 📊 Resultados registrados

<div align="center">

| ✅ Testes aprovados | 🎯 Assertions | ❌ Falhas |
|:---:|:---:|:---:|
| **20** | **55** | **0** |

**Última execução registrada: 23/11/2026**

</div>

Os testes incluem verificações de autenticação, autorização, isolamento de registros, associações entre clientes e APRs e integridade do banco de dados.

---

## 🗺️ Roadmap do projeto

### ✅ Funcionalidades implementadas

- [x] API REST para gerenciamento de clientes.
- [x] API REST para gerenciamento de APRs.
- [x] Relacionamentos entre usuários, clientes e APRs.
- [x] Cadastro de etapas e executantes.
- [x] Autenticação com Laravel Sanctum.
- [x] Autorização individual por usuário.
- [x] Validação da propriedade dos clientes.
- [x] Proteção contra alterações indevidas.
- [x] Migrations para integridade dos dados.
- [x] Testes automatizados de segurança.

### 🚧 Melhorias planejadas

- [ ] Modernização do Front-End.
- [ ] Dashboard com indicadores e informações organizadas.
- [ ] Interface responsiva para computadores e dispositivos móveis.
- [ ] Melhorias nos formulários de cadastro e edição.
- [ ] Mensagens de sucesso e tratamento visual de erros.
- [ ] Evolução da experiência de navegação.
- [ ] Geração e exportação de documentos APR.
- [ ] Ampliação da cobertura de testes.

---

## 🎯 Objetivo do desenvolvimento

Este projeto tem como objetivo aplicar conhecimentos de **Desenvolvimento de Sistemas e Engenharia de Software** em uma aplicação com regras de negócio, persistência de dados e mecanismos de segurança.

Durante seu desenvolvimento, são trabalhados conceitos como:

- Programação Back-End.
- Arquitetura de APIs REST.
- Orientação a Objetos.
- Modelagem de banco de dados.
- Autenticação e autorização.
- Segurança de aplicações.
- Testes automatizados.
- Organização e manutenção de código.

---

## 🔐 Boas práticas

- Não versionar arquivos `.env`.
- Não publicar credenciais ou chaves reais.
- Utilizar variáveis de ambiente.
- Validar os dados recebidos pela API.
- Restringir operações aos usuários autorizados.
- Executar testes antes de publicar alterações.
- Manter migrations e documentação atualizadas.

---

<div align="center">

### 🛡️ Sistema de Gestão e Emissão de APR

**Desenvolvimento de Sistemas • Laravel • PostgreSQL • API REST**

<sub>Projeto em desenvolvimento — documentação atualizada conforme a evolução da aplicação.</sub>

</div>
