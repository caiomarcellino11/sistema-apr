# 📋 Sistema de Gestão e Emissão de APR — SENAI

Aplicação web desenvolvida para digitalizar, padronizar e agilizar a criação, o gerenciamento e a emissão em PDF da **Análise Preliminar de Risco (APR)** no padrão oficial da unidade **SENAI Americana**.

---

## 🎯 Sobre o Projeto

Na rotina de oficinas, laboratórios e serviços técnicos, o preenchimento manual da Análise Preliminar de Risco (APR) demanda tempo e está sujeito a rasuras ou padronização inconsistente. Este sistema oferece um fluxo completo onde alunos, instrutores e técnicos podem cadastrar clientes, selecionar riscos pré-catalogados e gerar o documento oficial pronto para impressão ou salvamento em PDF em folha A4.

---

## ✨ Principais Funcionalidades

* **Autenticação e Isolamento de Dados:** Cadastro e login de usuários com autenticação via token (`Bearer Token`), garantindo que cada usuário visualize e gerencie exclusivamente as suas próprias APRs.
* **Cadastro Rápido de Clientes:** Registro completo dos dados do solicitante/empresa (endereço, contatos e e-mail) com vínculo direto no momento da criação da APR.
* **Catálogo Inteligente de Riscos e EPIs:** Preenchimento automático da descrição da etapa, riscos envolvidos e medidas de controle/EPIs com apenas um clique, permitindo edição livre após a inserção.
* **Etapas e Executantes Dinâmicos:** Adição dinâmica de múltiplas etapas da atividade e da equipe executante (nome, função e matrícula).
* **Layout Oficial de Impressão A4 (`imprimir.html`):**
  * Diagramação fiel ao formulário padrão do **SENAI Americana**.
  * Logotipo institucional moderno do **SENAI** renderizado em **SVG vetorial** (garantindo nitidez e cores fiéis na exportação para PDF independentemente das configurações de fundo do navegador).
  * Ajuste automático de margens e proporções para fechamento em folha **A4 Retrato** (`210mm x 297mm`).

---

## 🛠️ Tecnologias Utilizadas

### Backend
* **PHP 8.x** & **Laravel** (API RESTful)
* **PostgreSQL** (Banco de dados relacional)
* **Laravel Sanctum** (Autenticação via API Token)

### Frontend
* **HTML5** & **CSS3** (Estilização responsiva e regras dedicadas `@media print` para padrão A4)
* **JavaScript (ES6+)** (Consumo assíncrono via `Fetch API` e manipulação dinâmica do DOM)
* **SVG** (Identidade visual vetorial)

---

## 📁 Estrutura do Projeto

```text
├── backend/                  # API RESTful em Laravel
│   ├── app/
│   │   ├── Http/Controllers/ # Controladores (AuthController, AprController, ClienteController)
│   │   └── Models/           # Modelos Eloquent (User, Cliente, Apr, Etapa, Executante)
│   ├── database/migrations/  # Estrutura das tabelas no PostgreSQL
│   └── routes/api.php        # Definição das rotas da API
│
├── frontend/                 # Interface Web
│   ├── index.html            # Tela de Login e Cadastro de Usuários
│   ├── dashboard.html        # Painel principal, listagem e formulário de nova APR
│   └── imprimir.html         # Espelho oficial A4 para impressão e geração de PDF
│
└── README.md                 # Documentação do projeto
```

---

## 🚀 Como Executar Localmente

### Pré-requisitos
* **PHP** (>= 8.1) e **Composer**
* **PostgreSQL** ativo
* Navegador web moderno (Chrome, Edge ou Firefox)

### 1. Configurando o Backend (Laravel)

Acesse a pasta do backend e instale as dependências:

```bash
cd backend
composer install
```

Copie o arquivo de ambiente e configure as credenciais do seu banco **PostgreSQL**:

```bash
cp .env.example .env
php artisan key:generate
```

No arquivo `.env`, ajuste as variáveis de conexão com o banco:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=apr_senai
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

Execute as migrations para criar as tabelas e inicie o servidor local:

```bash
php artisan migrate
php artisan serve
```

A API estará disponível em `http://127.0.0.1:8000/api`.

### 2. Executando o Frontend

Com o servidor Laravel rodando, abra o arquivo `frontend/index.html` (ou `login.html`) diretamente no navegador ou através de um servidor estático local (como a extensão **Live Server** do VS Code).

> **Nota:** Certifique-se de que a constante `API_URL` nos arquivos do frontend esteja apontando para `http://127.0.0.1:8000/api`.

---

## 🔌 Principais Endpoints da API

| Método | Rota | Descrição | Autenticação |
| :--- | :--- | :--- | :---: |
| `POST` | `/api/register` | Cadastra um novo usuário | Não |
| `POST` | `/api/login` | Autentica o usuário e retorna o token | Não |
| `GET` | `/api/clientes` | Lista os clientes cadastrados | Sim |
| `POST` | `/api/clientes` | Cadastra um novo cliente | Sim |
| `GET` | `/api/aprs` | Lista as APRs do usuário autenticado | Sim |
| `POST` | `/api/aprs` | Cria uma nova APR com etapas e executantes | Sim |
| `GET` | `/api/aprs/{id}` | Retorna os detalhes completos de uma APR para impressão | Sim |

---

## 💡 Dicas de Uso

* **Gerando o PDF:** Na tela de visualização da ficha (`imprimir.html`), clique em **"Imprimir / Salvar PDF"** (ou pressione `Ctrl + P`), selecione o destino **"Salvar como PDF"** e mantenha o tamanho do papel em **A4**.
* **Campo CREA:** O formulário de impressão foi modernizado para omitir o campo CREA (visto que APRs em ambiente escolar/técnico são assinadas por instrutores ou técnicos de segurança). Caso o campo ainda esteja marcado como obrigatório no formulário de cadastro, basta preencher com `-`.