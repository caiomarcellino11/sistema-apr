# 🛡️ Sistema de Gestão e Automação de APR

Projeto Integrador desenvolvido para o módulo de **Banco de Dados** do curso Técnico em Desenvolvimento de Sistemas. O sistema digitaliza e automatiza o preenchimento da **APR (Análise Preliminar de Risco)**, substituindo o processo manual em papel por uma aplicação web integrada a um banco de dados relacional.

---

## 🎯 Objetivo e Funcionalidades

* **Automação de Cadastros:** Reaproveitamento dos dados cadastrais de clientes já registrados, evitando redigitação a cada nova ordem de serviço.
* **Etapas Dinâmicas de Risco:** Cadastro flexível de quantas etapas de trabalho, riscos envolvidos e medidas de controle forem necessárias (sem o limite de linhas fixas do formulário físico).
* **Controle de Equipe:** Vinculação dinâmica dos profissionais executantes (nome, função e matrícula) e do responsável técnico (CREA).
* **CRUD Completo:** Criação, listagem, consulta detalhada, edição e exclusão de clientes e APRs via API REST.

---

## 🛠️ Tecnologias Utilizadas

* **Back-end (API REST):** PHP 8+ com Framework **Laravel**
* **Banco de Dados:** **PostgreSQL** (`apr_db`)
* **Front-end:** HTML5, CSS3 e JavaScript puro (`Fetch API`)
* **Versionamento:** Git e GitHub

---

## 🗄️ Modelagem do Banco de Dados (`apr_db`)

O banco relacional foi estruturado em **4 tabelas principais** conectadas por chaves estrangeiras (*Foreign Keys*):

1. **`clientes`**: Armazena os dados cadastrais do solicitante (`nome`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `tel_residencial`, `tel_celular`, `email`).
2. **`aprs`**: Cabeçalho do documento (`cliente_id` [FK], `local`, `atividade`, `responsavel_nome`, `responsavel_crea`, `data_emissao`).
3. **`etapa_aprs`**: Tabela filha ligada à APR (`apr_id` [FK], `numero_etapa`, `descricao`, `riscos_envolvidos`, `medidas_controle`).
4. **`executante_aprs`**: Tabela filha ligada à APR (`apr_id` [FK], `nome`, `funcao`, `matricula`).

---

## 📁 Estrutura do Repositório

```text
sistema-apr/
├── backend/        # API REST desenvolvida em Laravel
├── frontend/       # Interface web em HTML, CSS e JS (Fetch)
└── README.md       # Documentação do projeto