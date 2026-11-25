
# 🛡️ Sistema de Gestão e Emissão de APR

**Sistema web para cadastro, gerenciamento e emissão de Análises Preliminares de Risco (APR).**

![Status](https://img.shields.io/badge/Status-Em_Desenvolvimento-yellow)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Database-4169E1)
![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-F7DF1E)

> **Última atualização:** 25/11/2026  
> **Status:** Em desenvolvimento  
> **Projeto:** Sistema APR — SENAI Americana

---

## 📖 Sobre o projeto

O Sistema de Gestão e Emissão de APR é uma aplicação web desenvolvida para facilitar a elaboração, organização e emissão de Análises Preliminares de Risco.

Seu objetivo é oferecer uma plataforma na qual usuários possam cadastrar clientes, registrar atividades, identificar riscos, definir medidas de controle e gerar documentos de APR.

O projeto também busca reduzir o preenchimento repetitivo por meio de catálogos de riscos e da reutilização de informações cadastradas anteriormente.

O sistema está sendo desenvolvido com foco em:

- Segurança da informação;
- Organização dos registros;
- Facilidade de preenchimento;
- Reutilização de informações;
- Experiência do usuário;
- Manutenção e escalabilidade.

> **Importante:** os riscos, EPIs e medidas de controle sugeridos pelo sistema precisam ser avaliados conforme a atividade real, as condições do ambiente e as normas aplicáveis. O preenchimento automático não substitui uma avaliação técnica de segurança.

---

## 🛠️ Tecnologias utilizadas

### Back-End

- PHP 8.3 ou superior;
- Laravel;
- Laravel Sanctum;
- Eloquent ORM;
- API REST;
- Composer.

### Front-End

- HTML5;
- CSS3;
- JavaScript;
- Fetch API.

### Banco de dados

- PostgreSQL;
- SQLite para testes automatizados;
- Migrations e Seeders do Laravel.

### Ferramentas

- Visual Studio Code;
- Git e GitHub;
- PowerShell;
- Composer;
- Live Server;
- PHPUnit.

---

## 📂 Estrutura do projeto

```text
sistema-apr/
│
├── README.md
│
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AprController.php
│   │   │       ├── AuthController.php
│   │   │       ├── ClienteController.php
│   │   │       └── CatalogoRiscoController.php
│   │   │
│   │   └── Models/
│   │       ├── Apr.php
│   │       ├── Cliente.php
│   │       ├── EtapaApr.php
│   │       ├── ExecutanteApr.php
│   │       ├── CatalogoRisco.php
│   │       └── User.php
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
│   │
│   ├── .env.example
│   ├── artisan
│   └── composer.json
│
└── frontend/
    ├── index.html
    ├── dashboard.html
    ├── imprimir.html
    └── style.css
```

---

## ✅ Funcionalidades desenvolvidas

### 1. Autenticação de usuários

O sistema possui autenticação utilizando Laravel Sanctum.

Funcionalidades:

- Cadastro de usuários;
- Login;
- Autenticação por token;
- Logout;
- Proteção de rotas;
- Identificação do usuário autenticado.

As informações de clientes e APRs são vinculadas ao usuário responsável.

### 2. Gerenciamento de clientes

Implementação de operações CRUD:

- Cadastrar clientes;
- Listar clientes;
- Consultar informações;
- Atualizar cadastros;
- Excluir clientes.

Cada usuário autenticado acessa apenas os clientes permitidos para sua conta.

### 3. Gerenciamento de APRs

O sistema permite:

- Criar APRs;
- Listar APRs;
- Consultar registros;
- Atualizar informações;
- Excluir APRs;
- Associar clientes;
- Cadastrar etapas;
- Registrar executantes;
- Informar riscos e EPIs.

### 4. Etapas e executantes

Cada APR pode conter etapas com informações específicas.

Entre os campos utilizados estão:

- Descrição da etapa;
- Riscos envolvidos;
- EPIs obrigatórios;
- Medidas de controle;
- Nome dos executantes;
- Função;
- Matrícula.

### 5. Catálogo automático de riscos e EPIs

Foi implementado um catálogo inicial de modelos.

Atualmente, existem seis modelos cadastrados:

1. Manutenção e inspeção elétrica;
2. Trabalho em altura;
3. Corte e soldagem;
4. Operação de máquinas e equipamentos;
5. Manuseio de produtos químicos;
6. Movimentação de cargas.

Os registros são armazenados no PostgreSQL.

A contagem do catálogo foi confirmada durante o desenvolvimento:

```text
CatalogoRisco::count() = 6
```

A integração do catálogo com o formulário deve continuar sendo testada.

### 6. Reutilização de dados de APRs anteriores

Foi preparada uma funcionalidade para selecionar uma APR anterior e aproveitar suas informações.

Campos previstos para preenchimento:

- Cliente;
- Local;
- Atividade;
- Nome do responsável;
- Registro profissional.

Os campos continuam editáveis.

**Situação:** código preparado; integração ainda precisa ser validada no navegador.

### 7. Impressão da APR

O arquivo `imprimir.html` apresenta o documento para impressão.

O usuário pode utilizar o recurso do navegador para imprimir ou salvar em PDF.

**Observação:** atualmente, essa funcionalidade utiliza a impressão do navegador, não um gerador de PDF no Back-End.

---

## 🔐 Segurança implementada

O Back-End possui:

- Autenticação com Laravel Sanctum;
- Middleware `auth:sanctum`;
- Isolamento de APRs por usuário;
- Isolamento de clientes por usuário;
- Validação da propriedade dos clientes;
- Validação de entradas;
- Uso do Eloquent ORM.

### Segurança contra XSS

Durante os testes manuais, foi identificada execução indevida de JavaScript inserido em um formulário.

Foram preparadas correções nos arquivos HTML para evitar interpretar conteúdo fornecido pelo usuário como código executável.

Entre as técnicas utilizadas estão:

```javascript
elemento.textContent = valor;
```

E o escape de conteúdo ao construir HTML.

**Pendente:** executar novos testes para confirmar que todos os campos e a impressão estão protegidos.

### Melhorias de segurança futuras

- Revisar o armazenamento dos tokens no navegador;
- Restringir alterações do catálogo a usuários autorizados;
- Ampliar testes de autorização;
- Revisar validação dos formulários;
- Implementar políticas de segurança do conteúdo;
- Proteger a aplicação em ambiente de produção;
- Revisar logs e tratamento de erros.

---

# 🚀 Como executar em outro computador

## 1. Pré-requisitos

Antes de iniciar, instale:

- PHP 8.3 ou superior;
- Composer;
- PostgreSQL;
- Git;
- Visual Studio Code;
- Extensão Live Server, se desejado.

Confirme a instalação:

```powershell
php -v
composer --version
git --version
```

Verifique as extensões do PHP:

```powershell
php -m
```

O PostgreSQL exige que a extensão `pdo_pgsql` esteja habilitada.

Para executar os testes com SQLite, também serão necessárias as extensões correspondentes.

## 2. Baixar o projeto

Se tiver acesso ao repositório:

```powershell
git clone https://github.com/caiomarcellino11/sistema-apr.git
```

Entre no diretório:

```powershell
cd sistema-apr
```

Depois:

```powershell
cd backend
```

Se o repositório estiver privado, utilize uma conta autorizada ou uma cópia ZIP do projeto.

## 3. Instalar dependências

Execute:

```powershell
composer install
```

Esse comando instala as dependências definidas no projeto.

## 4. Configurar o ambiente

Crie o arquivo `.env` a partir do exemplo:

```powershell
Copy-Item .env.example .env
```

Configure as informações do banco de dados:

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

Substitua pelos dados do seu PostgreSQL.

**Nunca publique o arquivo `.env` no GitHub.**

Para instalações novas, execute:

```powershell
php artisan key:generate
```

Depois:

```powershell
php artisan config:clear
```

Em instalações existentes, preserve a `APP_KEY` original quando ela for necessária para dados já criptografados.

## 5. Configurar o banco de dados

Crie um banco PostgreSQL para o projeto.

Para consultar o estado das migrations:

```powershell
php artisan migrate:status
```

Em um banco novo, após conferir a configuração, execute:

```powershell
php artisan migrate
```

**Atenção:** não utilize os comandos abaixo em um banco com informações que deseja preservar:

```text
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
```

Eles podem eliminar dados existentes.

### Transferência entre computadores

Clonar o repositório não transfere os registros PostgreSQL.

Para continuar utilizando os mesmos dados, será necessário:

- Conectar ao mesmo banco autorizado; ou
- Criar e restaurar um backup do PostgreSQL.

Não execute migrations destrutivas no banco original.

## 6. Carregar o catálogo de riscos

Execute:

```powershell
php artisan db:seed --class=CatalogoRiscoSeeder
```

Para verificar os registros:

```powershell
php artisan tinker --execute="dump(\App\Models\CatalogoRisco::count());"
```

O catálogo inicial possui seis modelos.

## 7. Iniciar o Laravel

Execute:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Endereço esperado:

```text
http://127.0.0.1:8000
```

Mantenha esse terminal aberto.

## 8. Iniciar o Front-End

Abra a pasta `frontend` no VS Code.

Com a extensão Live Server, abra:

```text
index.html
```

O endereço poderá ser:

```text
http://127.0.0.1:5500/frontend/index.html
```

A URL depende da pasta utilizada como raiz no Live Server.

O Front-End utiliza a API Laravel:

```javascript
const API_URL = 'http://127.0.0.1:8000/api';
```

Se os servidores estiverem em máquinas diferentes, será necessário configurar corretamente a conexão de rede.

## 9. Verificar o sistema

Após iniciar:

- [ ] A tela de login abre;
- [ ] O cadastro funciona;
- [ ] O login funciona;
- [ ] Clientes são carregados;
- [ ] APRs são carregadas;
- [ ] O catálogo apresenta os modelos;
- [ ] O histórico apresenta APRs anteriores;
- [ ] Os formulários permitem edição manual;
- [ ] A impressão funciona;
- [ ] Não existe execução indevida de JavaScript;
- [ ] Os registros existentes foram preservados.

---

# 🧪 Testes automatizados

O projeto utiliza PHPUnit.

Para executar:

```powershell
php artisan test
```

Os testes incluem verificações de autenticação e autorização.

Resultado histórico registrado durante o desenvolvimento:

```text
20 testes aprovados
55 assertions
```

Esse resultado não substitui a execução de novos testes após as alterações recentes.

---

# 🗺️ Roadmap — Próximas melhorias

## Fase 1 — Estabilização e segurança

**Prioridade: alta**

- [ ] Confirmar a correção de XSS;
- [ ] Testar a impressão com dados especiais;
- [ ] Validar o histórico de APRs;
- [ ] Confirmar que o catálogo carrega corretamente;
- [ ] Revisar permissões de alteração dos modelos;
- [ ] Executar todos os testes automatizados.

**Objetivo:** garantir que as funcionalidades atuais estejam estáveis antes de ampliar o projeto.

---

## Fase 2 — Modernização visual

**Prioridade: alta**

Melhorar o design e a experiência do usuário.

### Dashboard

- [ ] Criar um painel mais moderno;
- [ ] Melhorar menus e navegação;
- [ ] Adicionar cartões informativos;
- [ ] Exibir quantidade de clientes;
- [ ] Exibir quantidade de APRs;
- [ ] Adicionar pesquisa de APRs;
- [ ] Implementar filtros e ordenação;
- [ ] Melhorar mensagens de erro e sucesso.

### Formulários

- [ ] Organizar o cadastro em etapas;
- [ ] Melhorar espaçamento e tipografia;
- [ ] Criar componentes reutilizáveis;
- [ ] Melhorar campos de seleção;
- [ ] Adicionar indicadores de carregamento;
- [ ] Tornar a interface responsiva.

### Visualização da APR

- [ ] Melhorar o modelo de impressão;
- [ ] Organizar a apresentação dos riscos;
- [ ] Destacar os EPIs e medidas de controle;
- [ ] Melhorar o cabeçalho;
- [ ] Revisar quebras de página;
- [ ] Ajustar o documento para formato A4.

**Objetivo:** criar uma experiência mais profissional e intuitiva.

---

## Fase 3 — Expansão do catálogo de riscos e EPIs

**Prioridade: alta**

Ampliar as opções disponíveis para preenchimento automático.

### Novas funcionalidades

- [ ] Adicionar mais modelos de atividades;
- [ ] Organizar modelos por categoria;
- [ ] Criar pesquisa por nome;
- [ ] Permitir selecionar vários modelos;
- [ ] Exibir uma prévia antes do preenchimento;
- [ ] Evitar duplicação de informações;
- [ ] Manter campos totalmente editáveis;
- [ ] Criar modelos favoritos;
- [ ] Permitir modelos personalizados por usuário.

### Exemplos de atividades para expansão

- Limpeza e organização de ambientes;
- Inspeção de equipamentos;
- Manutenção preventiva;
- Armazenamento de materiais;
- Transporte interno;
- Montagem de componentes;
- Desmontagem de equipamentos;
- Inspeções de rotina;
- Atividades de manutenção predial.

**Importante:** os riscos e controles de cada modelo deverão ser revisados tecnicamente antes de disponibilização para uso real.

**Objetivo:** reduzir o preenchimento manual sem comprometer a análise de segurança.

---

## Fase 4 — Preenchimento inteligente

**Prioridade: média**

Ampliar o reaproveitamento de informações já registradas.

### Funcionalidades planejadas

- [ ] Selecionar responsáveis utilizados anteriormente;
- [ ] Recuperar executantes cadastrados;
- [ ] Reutilizar funções e matrículas;
- [ ] Sugerir locais frequentes;
- [ ] Recuperar atividades anteriores;
- [ ] Criar modelos favoritos de APR;
- [ ] Permitir duplicar uma APR.

### Duplicação de APR

O usuário poderá selecionar uma APR existente e criar um novo documento baseado nela.

Regras:

1. A APR original será preservada;
2. Uma nova APR será criada;
3. O usuário poderá editar os campos;
4. Datas e responsáveis precisarão ser revisados;
5. Riscos e EPIs deverão ser reavaliados;
6. Nenhum registro anterior será alterado automaticamente.

**Objetivo:** acelerar o trabalho de usuários que executam atividades semelhantes.

---

## Fase 5 — Segurança e qualidade

**Prioridade: alta antes de publicação**

- [ ] Revisar autenticação e armazenamento de tokens;
- [ ] Implementar autorização administrativa;
- [ ] Melhorar tratamento de erros;
- [ ] Ampliar testes de API;
- [ ] Realizar testes de segurança;
- [ ] Revisar acessibilidade;
- [ ] Preparar backups;
- [ ] Configurar HTTPS;
- [ ] Preparar implantação em servidor adequado.

---

# 🔜 Como continuar o projeto

Ao retornar ao desenvolvimento, seguir esta ordem:

1. Testar os arquivos HTML atualizados;
2. Confirmar que o XSS foi corrigido;
3. Validar o preenchimento pelo histórico;
4. Melhorar o visual do dashboard;
5. Reorganizar os formulários;
6. Ampliar o catálogo de riscos e EPIs;
7. Adicionar pesquisa e categorias;
8. Implementar reutilização de executantes;
9. Criar a funcionalidade de duplicar APR;
10. Executar testes e atualizar a documentação.

---

## ⚠️ Cuidados durante o desenvolvimento

- Fazer backup antes de alterações importantes;
- Não excluir dados antigos sem necessidade;
- Não publicar arquivos `.env`;
- Não compartilhar credenciais;
- Utilizar dados fictícios nos testes;
- Revisar a segurança do Front-End;
- Executar os testes após mudanças;
- Não utilizar modelos automáticos como substitutos de uma análise técnica.

---

## 📌 Status atual

O projeto possui uma estrutura funcional de API REST, autenticação, gerenciamento de clientes e APRs, catálogo inicial e interface para impressão.

As melhorias recentes de proteção contra XSS e reutilização de dados anteriores ainda precisam de validação completa no navegador.

As próximas prioridades serão **modernizar a interface** e **expandir o preenchimento automático de riscos e EPIs**.

---

**Sistema APR — Projeto educacional em desenvolvimento.**
