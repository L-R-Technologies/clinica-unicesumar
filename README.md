# Base Laravel Blade

## Pré-requisitos

Antes de rodar o projeto, instale na sua máquina:

- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

Opcional para desenvolvimento local sem Docker:
- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/)

## Como rodar o projeto

1. **Clone o repositório:**
	```bash
	git clone git@github.com:L-R-Technologies/base-laravel-blade.git
	cd base-laravel-blade
	```

2. **Copie o arquivo de ambiente:**
	```bash
	cp .env.example .env
	```

3. **Ajuste as variáveis do `.env`:**
	- APP_NAME, APP_URL, APP_KEY
	- DB_DATABASE, DB_USERNAME, DB_PASSWORD
	- MAIL_FROM_ADDRESS, MAIL_FROM_NAME
	- Outras variáveis conforme sua necessidade

4. **Suba os containers Docker:**
	```bash
	./vendor/bin/sail up -d
	```


5. **Instale as dependências:**
	```bash
	./vendor/bin/sail composer install
	```

6. **Gere a chave da aplicação:**
	```bash
	./vendor/bin/sail artisan key:generate
	```

7. **Execute as migrations:**
	```bash
	./vendor/bin/sail artisan migrate
	```

## O que personalizar após baixar

- Remova ou adicione migrations conforme seu projeto
- Ajuste as configurações do `.env` para seu ambiente
- Edite ou remova views de exemplo em `resources/views`
- Adicione suas rotas em `routes/web.php`
- Instale pacotes PHP/JS conforme necessidade
- Atualize o README com instruções específicas do seu projeto

## Observações

- O template já está pronto para autenticação com Fortify
- Docker Compose já inclui MySQL, Redis e Mailpit
