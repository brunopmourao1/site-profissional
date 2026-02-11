# Diario de Projeto

## 2026-02-10 - Inicio e estrutura
- Projeto inicialmente vazio.
- Criadas pastas base: `admin`, `assets`, `includes`, `data`, `uploads`.
- Definida arquitetura em PHP puro + JSON para facilitar hospedagem comum.

## 2026-02-10 - Implementacao MVP funcional
- Criado `index.php` com renderização dinâmica de seções.
- Criados módulos de dados:
  - `includes/bootstrap.php`
  - `includes/site_data.php`
  - `includes/auth.php`
- Criado painel:
  - `admin/login.php`
  - `admin/index.php`
  - `admin/logout.php`
- Criado dataset inicial: `data/site.json`.
- Criado CSS/JS base e depois refino visual premium (`assets/site.css`, `assets/site.js`).

## 2026-02-10 - Situação de testes
- Tentativa de validação por CLI (`php -l`) não concluída por ausência de PHP no terminal.
- Testes formais pendentes de execução local com PHP instalado.

## Decisões técnicas tomadas
- Stack sem framework para reduzir complexidade inicial e facilitar deploy.
- Persistência em JSON para edição rápida e sem banco de dados no MVP.
- Upload de imagens armazenado localmente em `uploads/`.
- Sessão PHP para autenticação do dashboard.

## Proximo checkpoint
- Executar plano de testes de ponta a ponta.
- Aplicar correções.
- Congelar escopo e publicar versão 1.0.
