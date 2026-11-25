
<div align="center">

# 🛡️ Sistema APR

### Plataforma Web para Gestão de Análises Preliminares de Risco

**Cadastro de clientes, elaboração de APRs, catálogo de riscos e emissão de documentos para impressão.**

![Status](https://img.shields.io/badge/Status-Em_Desenvolvimento-F0AD4E?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

**Projeto educacional — SENAI Americana**

**Última atualização:** 25/11/2026

</div>

---

## 📖 Sobre o Projeto

O **Sistema APR** é uma aplicação web desenvolvida para apoiar a criação, o gerenciamento e a emissão de documentos de **Análise Preliminar de Risco (APR)**.

A plataforma centraliza informações relacionadas a clientes, atividades profissionais, etapas operacionais, riscos, equipamentos de proteção individual (EPIs) e medidas de controle.

A aplicação utiliza uma **API REST desenvolvida em Laravel**, com autenticação via Laravel Sanctum, persistência de dados em PostgreSQL e interface construída com HTML, CSS e JavaScript.

O projeto busca proporcionar:

- ✅ Centralização e organização das APRs;
- ✅ Cadastro e gerenciamento de clientes;
- ✅ Registro de atividades e etapas operacionais;
- ✅ Identificação e documentação de riscos;
- ✅ Preenchimento auxiliado por modelos cadastrados;
- ✅ Reutilização de informações de APRs anteriores;
- ✅ Emissão de documentos para impressão;
- ✅ Autenticação e isolamento dos registros por usuário.

> **Nota de segurança:** os modelos automáticos são referências iniciais. A identificação dos riscos, a escolha dos EPIs e as medidas de controle devem ser revisadas por profissionais competentes, considerando as condições reais de trabalho e as normas aplicáveis.

---

## 🚀 Estado Atual do Projeto

A versão atual contempla uma API REST, gerenciamento de clientes e APRs, catálogo inicial de riscos, autenticação e interface para preenchimento e impressão.

| Módulo | Funcionalidade | Situação |
|---|---|---|
| 🔐 Autenticação | Cadastro, login e logout com Sanctum | ✅ Implementado |
| 👥 Clientes | CRUD com isolamento por usuário | ✅ Implementado |
| 📋 APRs | Cadastro, consulta, edição e exclusão | ✅ Implementado |
| 🧩 Etapas | Registro de etapas e riscos | ✅ Implementado |
| 👷 Executantes | Associação de participantes às APRs | ✅ Implementado |
| 🦺 Catálogo | Seis modelos iniciais de atividades | ✅ Implementado |
| ⚡ Preenchimento automático | Preenchimento a partir do catálogo | 🧪 Validar integração |
| 🔄 Histórico | Reutilização de dados básicos de APRs anteriores | 🧪 Validar integração |
| 🖨️ Impressão | Visualização A4 e impressão pelo navegador | ✅ Implementado |
| 🛡️ Proteção contra XSS | Correções no Front-End | 🧪 Requer novos testes |
| 📊 Dashboard avançado | Indicadores, filtros e pesquisa | 📅 Planejado |
| 📄 PDF no servidor | Geração automática pelo Back-End | 📅 Planejado |

**Observação:** os recursos classificados como implementados estão presentes no código. Isso não significa que todos tenham sido validados em ambiente de produção.

---

## 🛠️ Tecnologias Utilizadas

### Back-End

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=flat-square&logo=composer&logoColor=white)

- PHP 8.3 ou superior;
- Laravel 13;
- Laravel Sanctum 4;
- Eloquent ORM;
- API REST;
- Composer;
- Validação de requisições;
- Relacionamentos entre modelos.

### Front-End

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)

- HTML5;
- CSS3;
- JavaScript;
- Fetch API;
- Manipulação do DOM;
- Integração com endpoints REST.

### Banco de Dados

![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=flat-square&logo=postgresql&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat-square&logo=sqlite&logoColor=white)

- PostgreSQL como banco principal;
- SQLite para testes;
- Migrations;
- Seeders;
- Relacionamentos e chaves estrangeiras.

### Desenvolvimento e Testes

![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white)
![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat-square&logo=github&logoColor=white)
![VS Code](https://img.shields.io/badge/VS_Code-007ACC?style=flat-square)
![PHPUnit](https://img.shields.io/badge/PHPUnit-Testes-366488?style=flat-square)

---

## 🏗️ Arquitetura da Aplicação

O sistema utiliza uma arquitetura baseada na comunicação entre um Front-End independente e uma API REST.

```text
              USUÁRIO
                 │
                 ▼
       ┌──────────────────┐
       │     FRONT-END    │
       │ HTML / CSS / JS  │
       └──────────────────┘
                 │
                 │ HTTP / JSON
                 ▼
       ┌──────────────────┐
       │    LARAVEL API   │
       │ Controllers      │
       │ Validações       │
       │ Sanctum          │
       └──────────────────┘
                 │
                 ▼
       ┌──────────────────┐
       │   ELOQUENT ORM   │
       └──────────────────┘
                 │
                 ▼
       ┌──────────────────┐
       │    POSTGRESQL    │
       └──────────────────┘
```

### Organização do Código

```text
sistema-apr/
│
├── README.md
│
├── backend/
│   │
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AuthController.php
│   │   │       ├── ClienteController.php
│   │   │       ├── AprController.php
│   │   │       └── CatalogoRiscoController.php
│   │   │
│   │   └── Models/
│   │       ├── User.php
│   │       ├── Cliente.php
│   │       ├── Apr.php
│   │       ├── EtapaApr.php
│   │       ├── ExecutanteApr.php
│   │       └── CatalogoRisco.php
│   │
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   │       └── CatalogoRiscoSeeder.php
│   │
│   ├── routes/
│   │   └── api.php
│   │
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   │
│   ├── composer.json
│   ├── .env.example
│   └── artisan
│
└── frontend/
    ├── index.html
    ├── dashboard.html
    ├── imprimir.html
    └── style.css
```

---

## 🔐 Autenticação e Segurança

A autenticação é implementada utilizando **Laravel Sanctum**.

### Funcionalidades

- Cadastro de usuários;
- Login e logout;
- Autenticação por Bearer Token;
- Middleware `auth:sanctum`;
- Proteção das rotas privadas;
- Associação de clientes e APRs ao usuário autenticado.

### Isolamento de Dados

Os registros de clientes e APRs são associados à conta responsável.

A aplicação consulta esses registros utilizando os relacionamentos do usuário autenticado.

Exemplo:

```php
$request->user()->aprs()->get();
```

Essa abordagem contribui para impedir que usuários consultem informações pertencentes a outras contas.

A criação e a atualização de APRs também verificam se o cliente selecionado pertence ao usuário autenticado.

### Segurança contra XSS

Durante os testes de desenvolvimento, foi identificada uma situação em que conteúdo inserido no formulário poderia executar JavaScript.

Foram preparadas correções no Front-End para utilizar tratamento seguro de conteúdo.

Exemplo:

```javascript
elemento.textContent = valor;
```

O recurso `textContent` permite exibir informações sem interpretá-las como HTML executável.

Também foram aplicadas melhorias no tratamento dos dados da página de impressão.

**Status:** as correções precisam passar por novos testes no navegador para confirmar sua eficácia.

### Melhorias de Segurança Pendentes

- [ ] Revisar o armazenamento de tokens no navegador;
- [ ] Restringir alterações do catálogo por perfil administrativo;
- [ ] Executar testes de regressão de XSS;
- [ ] Revisar mensagens de erro da API;
- [ ] Implementar medidas adicionais de proteção de conteúdo;
- [ ] Revisar políticas de autorização;
- [ ] Configurar HTTPS em produção;
- [ ] Implementar rotinas de backup e recuperação.

> O projeto ainda não passou por uma auditoria completa de segurança.

---

## 👥 Gerenciamento de Clientes

O sistema possui operações de CRUD para cadastro e gerenciamento de clientes.

### Funcionalidades

- Criar clientes;
- Listar registros;
- Consultar informações;
- Atualizar dados;
- Excluir clientes;
- Associar clientes ao usuário autenticado.

Cada cliente possui um vínculo com sua respectiva conta.

---

## 📋 Gerenciamento de APRs

O módulo principal permite cadastrar e gerenciar Análises Preliminares de Risco.

### Informações da APR

- Cliente;
- Local da atividade;
- Descrição da atividade;
- Responsável técnico;
- Registro profissional;
- Data de emissão;
- Etapas operacionais;
- Riscos envolvidos;
- EPIs e medidas de controle;
- Executantes.

### Etapas e Executantes

Uma APR pode possuir múltiplas etapas e participantes.

Cada etapa armazena sua descrição, os riscos envolvidos e as medidas de controle informadas.

Os executantes podem ser associados ao documento com nome, função e matrícula.

---

## 🦺 Catálogo de Riscos e EPIs

O projeto possui um catálogo com modelos destinados a auxiliar o preenchimento de APRs.

O catálogo inicial contém **seis modelos** cadastrados por meio de um Seeder do Laravel.

### Categorias Disponíveis

| Categoria | Exemplo de atividade |
|---|---|
| ⚡ Elétrica | Manutenção e inspeção elétrica |
| 🪜 Trabalho em altura | Instalação e manutenção em altura |
| 🔥 Trabalho a quente | Corte e soldagem |
| ⚙️ Máquinas | Operação de máquinas e equipamentos |
| 🧪 Produtos químicos | Manuseio de substâncias químicas |
| 📦 Movimentação de cargas | Transporte e içamento de materiais |

### Preenchimento Automático

Ao selecionar um modelo, o Front-End foi desenvolvido para preencher:

1. Descrição da etapa;
2. Riscos envolvidos;
3. EPIs e medidas de controle.

As informações continuam editáveis para adequação à atividade executada.

**Status:** os modelos estão cadastrados, mas o fluxo completo de seleção e preenchimento deve ser validado na interface.

### Expansão Planejada

O catálogo será ampliado com novas categorias e atividades comuns, utilizando referências revisadas tecnicamente.

Melhorias planejadas:

- Pesquisa de atividades;
- Filtros por categoria;
- Seleção de múltiplos modelos;
- Favoritos;
- Modelos personalizados;
- Organização individual de riscos, EPIs e medidas de controle;
- Remoção de duplicidades no preenchimento.

---

## 🔄 Reutilização de APRs Anteriores

O Front-End já contém uma funcionalidade para reutilizar dados básicos de APRs cadastradas anteriormente.

### Informações Reutilizáveis

- Cliente;
- Local;
- Atividade;
- Nome do responsável;
- Registro do responsável.

O objetivo é facilitar a criação de novas APRs sem exigir o preenchimento repetitivo dessas informações.

### Regras

- A APR original não é modificada;
- O usuário pode editar os dados recuperados;
- As informações são obtidas do histórico acessível à conta autenticada;
- As datas, etapas e executantes não são copiados automaticamente nesta versão.

**Status:** funcionalidade implementada no JavaScript, pendente de testes completos de integração.

---

## 🖨️ Visualização e Impressão

O sistema possui uma página dedicada à visualização do documento de APR.

O arquivo `imprimir.html` organiza os dados para impressão em formato A4.

### Recursos

- Apresentação dos dados da APR;
- Informações do cliente;
- Responsável;
- Atividades;
- Etapas;
- Riscos;
- Medidas de controle;
- Executantes;
- Impressão pelo navegador.

O documento pode ser salvo em PDF utilizando a opção de impressão do navegador.

**Limitação atual:** o projeto não possui geração independente de PDF no Back-End.

---

# ⚙️ Instalação e Execução

Esta seção apresenta os procedimentos para configurar o projeto em outro computador.

## 1. Pré-requisitos

Instale:

- PHP 8.3 ou superior;
- Composer;
- PostgreSQL;
- Git;
- Visual Studio Code;
- Extensão Live Server ou outro servidor HTTP local.

Para executar testes com SQLite, habilite as extensões `pdo_sqlite` e `sqlite3`.

Verifique as ferramentas:

```powershell
php -v
composer --version
git --version
php -m
```

Para conexão com PostgreSQL, a extensão `pdo_pgsql` precisa estar habilitada.

## 2. Clonar o Repositório

```powershell
git clone https://github.com/caiomarcellino11/sistema-apr.git
```

Acesse o projeto:

```powershell
cd sistema-apr
```

Depois:

```powershell
cd backend
```

Caso o repositório esteja privado, será necessário possuir acesso autorizado.

Também é possível utilizar uma cópia ZIP do projeto.

## 3. Instalar as Dependências

Na pasta `backend`, execute:

```powershell
composer install
```

O Composer instalará as dependências definidas no projeto.

## 4. Configurar o Ambiente

Crie o arquivo `.env`:

```powershell
Copy-Item .env.example .env
```

Configure o PostgreSQL:

```dotenv
APP_NAME="Sistema APR"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

Substitua os valores pelas informações da instalação.

Em uma instalação nova, gere a chave da aplicação:

```powershell
php artisan key:generate
```

Limpe a configuração armazenada em cache:

```powershell
php artisan config:clear
```

**Importante:** não substitua a `APP_KEY` de um ambiente existente sem avaliar os impactos sobre informações criptografadas e sessões.

Nunca publique o arquivo `.env`.

## 5. Configurar o Banco de Dados

Crie um banco PostgreSQL e confira sua conexão.

Antes de executar migrations, verifique o banco configurado e consulte:

```powershell
php artisan migrate:status
```

Em um banco de dados novo:

```powershell
php artisan migrate
```

### Preservação dos Dados

Não utilize os comandos abaixo em bancos com informações que precisam ser preservadas:

```powershell
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
```

Esses comandos podem remover dados existentes.

Clonar o projeto em outro computador não transfere automaticamente o banco PostgreSQL.

Para manter os registros anteriores, é necessário restaurar um backup ou conectar-se ao banco existente com autorização.

## 6. Popular o Catálogo

Execute:

```powershell
php artisan db:seed --class=CatalogoRiscoSeeder
```

Verifique a quantidade:

```powershell
php artisan tinker --execute="dump(\App\Models\CatalogoRisco::count());"
```

O catálogo inicial possui seis modelos.

O Seeder utiliza `firstOrCreate`, e deve ser executado explicitamente porque não está incluído no fluxo atual de `DatabaseSeeder`.

## 7. Iniciar o Servidor Laravel

Na pasta `backend`, execute:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Endereço da API:

```text
http://127.0.0.1:8000/api
```

Mantenha o servidor em execução durante a utilização do Front-End.

## 8. Iniciar o Front-End

Abra a pasta `frontend` no Visual Studio Code.

Execute `index.html` utilizando Live Server ou outro servidor HTTP local.

Exemplo:

```text
http://127.0.0.1:5500/frontend/index.html
```

A URL pode variar conforme a configuração do servidor.

Os arquivos HTML utilizam a API:

```javascript
const API_URL = 'http://127.0.0.1:8000/api';
```

Se a API estiver em outro computador, será necessário ajustar o endereço e as configurações de rede e CORS.

---

## 🔌 Endpoints da API

Prefixo das rotas:

```text
/api
```

### Autenticação

| Método | Endpoint | Descrição |
|---|---|---|
| `POST` | `/register` | Cadastro |
| `POST` | `/login` | Login |
| `GET` | `/user` | Usuário autenticado |
| `POST` | `/logout` | Logout |

### Clientes

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/clientes` | Listar clientes |
| `POST` | `/clientes` | Cadastrar cliente |
| `GET` | `/clientes/{id}` | Consultar cliente |
| `PUT/PATCH` | `/clientes/{id}` | Atualizar cliente |
| `DELETE` | `/clientes/{id}` | Excluir cliente |

### APRs

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/aprs` | Listar APRs |
| `POST` | `/aprs` | Criar APR |
| `GET` | `/aprs/{id}` | Consultar APR |
| `PUT/PATCH` | `/aprs/{id}` | Atualizar APR |
| `DELETE` | `/aprs/{id}` | Excluir APR |

### Catálogo

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/catalogo-riscos` | Listar modelos |
| `POST` | `/catalogo-riscos` | Cadastrar modelo |
| `GET` | `/catalogo-riscos/{id}` | Consultar modelo |
| `PUT/PATCH` | `/catalogo-riscos/{id}` | Atualizar modelo |
| `DELETE` | `/catalogo-riscos/{id}` | Excluir modelo |

As rotas de clientes, APRs e catálogo exigem autenticação.

---

## 🧪 Testes Automatizados

O projeto utiliza PHPUnit.

Execute:

```powershell
php artisan test
```

O código enviado contém testes relacionados a:

- Autenticação;
- Autorização;
- Isolamento de APRs;
- Isolamento de clientes;
- Configuração de banco;
- Estrutura de tabelas.

A versão enviada possui 20 métodos de teste identificados nos arquivos de testes.

**Os testes não foram executados nesta revisão.** Portanto, seus resultados precisam ser confirmados no ambiente de desenvolvimento.

### Checklist de Validação

- [ ] Realizar login com uma conta de teste;
- [ ] Verificar o logout;
- [ ] Testar o cadastro de clientes;
- [ ] Criar uma APR;
- [ ] Adicionar etapas;
- [ ] Adicionar executantes;
- [ ] Verificar o carregamento do catálogo;
- [ ] Testar o preenchimento automático;
- [ ] Testar a reutilização de APRs anteriores;
- [ ] Conferir a impressão A4;
- [ ] Validar as correções de XSS;
- [ ] Testar o isolamento entre usuários;
- [ ] Executar PHPUnit.

---

# 🗺️ Roadmap de Desenvolvimento

## 🔴 Fase 1 — Estabilidade e Segurança

**Prioridade: Alta**

- [ ] Validar as correções de XSS;
- [ ] Revisar as permissões do catálogo;
- [ ] Testar o preenchimento pelo histórico;
- [ ] Verificar a integração dos modelos de riscos;
- [ ] Revisar autenticação e tokens;
- [ ] Melhorar tratamento de erros;
- [ ] Executar os testes automatizados.

**Objetivo:** consolidar as funcionalidades existentes antes de ampliar a aplicação.

---

## 🎨 Fase 2 — Modernização da Interface

**Prioridade: Alta**

### Dashboard

- [ ] Modernizar o design;
- [ ] Melhorar a navegação;
- [ ] Adicionar indicadores;
- [ ] Exibir quantidade de clientes;
- [ ] Exibir quantidade de APRs;
- [ ] Implementar pesquisa;
- [ ] Implementar filtros;
- [ ] Melhorar notificações.

### Formulários

- [ ] Organizar o preenchimento por etapas;
- [ ] Melhorar os campos de seleção;
- [ ] Implementar indicadores de progresso;
- [ ] Criar componentes reutilizáveis;
- [ ] Melhorar responsividade;
- [ ] Adaptar a interface para dispositivos móveis.

### Impressão

- [ ] Melhorar o cabeçalho da APR;
- [ ] Reorganizar a apresentação de riscos e EPIs;
- [ ] Melhorar a legibilidade;
- [ ] Corrigir quebras de página;
- [ ] Padronizar o layout A4.

**Objetivo:** oferecer uma experiência mais profissional, moderna e intuitiva.

---

## 🦺 Fase 3 — Expansão do Catálogo

**Prioridade: Alta**

### Funcionalidades Planejadas

- [ ] Ampliar os modelos disponíveis;
- [ ] Organizar os modelos por categoria;
- [ ] Implementar pesquisa rápida;
- [ ] Permitir seleção de múltiplos modelos;
- [ ] Evitar duplicação de informações;
- [ ] Implementar favoritos;
- [ ] Criar modelos personalizados;
- [ ] Melhorar a separação de riscos, EPIs e medidas;
- [ ] Permitir pré-visualização antes do preenchimento.

### Exemplos de Atividades para Estudo

- Manutenção preventiva;
- Inspeção de equipamentos;
- Limpeza industrial;
- Manutenção predial;
- Armazenamento de materiais;
- Transporte interno;
- Montagem de componentes;
- Desmontagem de equipamentos;
- Inspeções de rotina.

Os modelos deverão ser revisados tecnicamente antes de serem disponibilizados para uso real.

**Objetivo:** facilitar o preenchimento mantendo a precisão das informações.

---

## 🔄 Fase 4 — Histórico e Automação

**Prioridade: Média**

- [ ] Reutilizar executantes cadastrados;
- [ ] Recuperar responsáveis anteriores;
- [ ] Sugerir locais frequentes;
- [ ] Sugerir atividades utilizadas anteriormente;
- [ ] Criar modelos favoritos de APR;
- [ ] Implementar duplicação completa;
- [ ] Oferecer prévia dos dados recuperados.

### Duplicação de APR

A duplicação permitirá criar uma nova APR a partir de um documento existente.

Regras previstas:

1. A APR original será preservada;
2. Um novo documento será criado;
3. Os campos permanecerão editáveis;
4. Datas e responsáveis deverão ser conferidos;
5. Riscos e medidas de controle deverão ser reavaliados;
6. O usuário deverá revisar as informações antes de salvar.

---

## 🔒 Fase 5 — Preparação para Produção

**Prioridade: Alta antes da publicação**

- [ ] Revisar permissões e autenticação;
- [ ] Configurar HTTPS;
- [ ] Desativar o modo debug;
- [ ] Revisar gerenciamento de segredos;
- [ ] Implementar backups;
- [ ] Revisar logs;
- [ ] Melhorar acessibilidade;
- [ ] Executar testes de segurança;
- [ ] Definir ambiente de hospedagem.

---

## 📌 Próximos Passos Imediatos

Ao retomar o desenvolvimento, seguir esta ordem:

1. Executar os testes automatizados;
2. Confirmar as correções de XSS;
3. Validar o histórico de APRs;
4. Testar os seis modelos do catálogo;
5. Corrigir as permissões de edição do catálogo;
6. Iniciar a modernização visual do dashboard;
7. Melhorar os formulários;
8. Expandir as opções de riscos e EPIs;
9. Implementar reutilização de executantes;
10. Desenvolver duplicação completa de APRs.

---

## ⚠️ Observações Importantes

- O sistema está em desenvolvimento;
- As sugestões automáticas não substituem avaliações técnicas;
- Dados sensíveis não devem ser enviados ao GitHub;
- Alterações no banco devem preservar os registros existentes;
- Testes devem utilizar informações fictícias;
- A aplicação precisa de validação adicional antes de ser publicada em produção.

---

<div align="center">

### 🛡️ Sistema APR

**Gestão de Análises Preliminares de Risco**

Projeto educacional em desenvolvimento — SENAI Americana

**PHP • Laravel • PostgreSQL • JavaScript**

*Documentação atualizada em 25 de novembro de 2026.*

</div>
