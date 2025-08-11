
# Clínica Unicesumar

Sistema web para gestão de exames laboratoriais realizados por alunos, com rastreabilidade de amostras, controle de calibração de equipamentos e conformidade LGPD.

## Sumário

- [1. Introdução](#1-introdução)
- [2. Visão Geral](#2-visão-geral)
- [3. Modelagem de Dados](#3-modelagem-de-dados)
- [4. Requisitos Específicos](#4-requisitos-específicos)
- [5. Casos de Uso](#5-casos-de-uso)
- [6. Instalação e Execução](#6-instalação-e-execução)
- [7. Tecnologias Utilizadas](#7-tecnologias-utilizadas)

---

## 1. Introdução

O sistema gerencia exames realizados por alunos no laboratório da Unicesumar, supervisionados por professores, com controle de equipamentos e rastreabilidade de amostras. Atende requisitos de segurança, privacidade e acessibilidade.

---

## 2. Visão Geral

- **Aplicação web responsiva** com autenticação por perfil (aluno, professor, paciente).
- **Gestão de exames**, rastreio de amostras e controle de calibração de equipamentos.
- **Controle de acesso** e conformidade LGPD.

### Funções Principais

- Cadastro e gerenciamento de usuários
- Registro, consulta e exportação de exames
- Rastreabilidade de amostras
- Controle de calibração de equipamentos
- Dashboards analíticos por perfil

### Usuários

- **Aluno:** registra e visualiza seus exames
- **Professor:** supervisiona, aprova e gerencia exames, máquinas e calibrações
- **Paciente:** visualiza seus resultados

### Restrições

- Web responsivo
- Backend: Laravel
- Banco: MySQL
- LGPD compliance

---

## 3. Modelagem de Dados

O sistema utiliza um banco relacional com as seguintes principais tabelas:

- **users:** Usuários do sistema (aluno, professor, paciente)
- **teachers, students, patients:** Perfis específicos vinculados ao usuário
- **address:** Endereços de pacientes
- **patient_histories:** Anamnese e histórico clínico
- **samples:** Amostras biológicas rastreadas por código único
- **exams:** Exames laboratoriais vinculados a amostras e pacientes
- **machines:** Equipamentos laboratoriais
- **calibrations:** Registros de calibração de máquinas
- **activity_log:** Auditoria de ações no sistema

Consulte o dicionário de dados detalhado na documentação para campos e relacionamentos.

---

## 4. Requisitos Específicos

### Requisitos Funcionais

- Cadastro, edição e visualização de exames por alunos
- Professores podem aprovar, editar, excluir e visualizar todos os exames
- Cadastro e gerenciamento de pacientes, amostras, máquinas e calibrações
- Notificações por e-mail para eventos relevantes
- Exportação de exames em PDF/Excel
- Dashboards analíticos por perfil
- Controle de acesso e rastreabilidade
- Conformidade LGPD e registro de consentimento

### Requisitos Não Funcionais

- Interface web responsiva e acessível (WCAG AA)
- Criptografia de dados sensíveis
- Limite de tentativas de login
- Compatibilidade com navegadores modernos
- Tempo de resposta inferior a 2 segundos para operações críticas

---

## 5. Casos de Uso

Principais fluxos implementados:

- **UC001:** Autenticação de usuário
- **UC002:** Logout seguro
- **UC003:** Recuperação de senha
- **UC004:** Gerenciamento de perfil
- **UC005:** Cadastro de novo exame
- **UC006:** Consulta de histórico de exames
- **UC007:** Visualização detalhada de exame
- **UC008:** Edição de exame
- **UC009:** Exclusão de exame
- **UC010:** Exportação de exame em PDF
- **UC011:** Visualização de dashboard
- **UC012:** Aprovação/rejeição de exames
- **UC013:** Gerenciamento de usuários
- **UC014:** Cadastro de paciente com consentimento LGPD
- **UC015:** Visualização de exames pelo paciente
- **UC016-UC020:** Gestão de amostras (CRUD)
- **UC021-UC025:** Gestão de máquinas (CRUD)
- **UC026-UC030:** Gestão de calibrações (CRUD)

Para detalhes completos de cada caso de uso, consulte a seção de especificação funcional.

---

## 6. Instalação e Execução

### Pré-requisitos

- Docker e Docker Compose
- Git
- PHP 8.1+
- Composer

### Passos

1. Clone o repositório:
	```bash
	git clone git@github.com:L-R-Technologies/clinica-unicesumar.git
	cd clinica-unicesumar
	```

2. Copie o arquivo de ambiente:
	```bash
	cp .env.example .env
	```

3. Instale as dependências:
	```bash
	composer install
	```

4. Suba os containers Docker:
	```bash
	./vendor/bin/sail up -d
	```

5. Entre no container do app para rodar comandos:
	```bash
	docker exec -u sail -it clinica-unicesumar-app-1 bash
	```

6. Instale/atualize dependências dentro do container:
	```bash
	composer install
	```

7. Gere a chave da aplicação:
	```bash
	php artisan key:generate
	```

8. Execute as migrations:
	```bash
	php artisan migrate
	```

9. Execute as seeds:
	```bash
	php artisan db:seed
	```

---

## 7. Tecnologias Utilizadas

- **Backend:** Laravel
- **Frontend:** Blade, Livewire
- **Banco de Dados:** MySQL
- **Containerização:** Docker, Laravel Sail
- **Testes:** PHPUnit

## 8. Troubleshooting

### Container do MySQL não sobe

Se o container do MySQL não estiver subindo, pode ser que exista um arquivo `.lock` impedindo a inicialização. Siga estes passos para remover o arquivo com segurança:

1. **Pare o serviço MySQL/Sail antes de manipular volumes.**
	```bash
	./vendor/bin/sail down
	```

2. **Liste os volumes disponíveis:**
	```bash
	docker volume ls
	```

3. **Identifique o nome do volume usado pelo serviço** (exemplo: `clinica-unicesumar_sail-mysql`).

4. **Rode um container temporário com o volume montado:**
	```bash
	docker run --rm -it -v NOME_DO_VOLUME:/data alpine sh
	```
	Exemplo:
	```bash
	docker run --rm -it -v clinica-unicesumar_sail-mysql:/data alpine sh
	```

5. **Dentro do shell do container, navegue até a pasta do volume e identifique arquivos `.lock`:**
	```sh
	cd /data
	ls
	```

6. **Delete o arquivo de lock:**
	```sh
	rm -rf mysql.sock.lock
	```

7. **Digite `exit` para sair do container.**

8. **Tente subir o container novamente:**
	```bash
	./vendor/bin/sail up -d
	```

> **Atenção:** Remova arquivos `.lock` apenas se tiver certeza que o serviço está parado e não há processos usando o volume, para evitar corrupção de dados.
