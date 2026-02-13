# Site Profissional com Dashboard

Projeto institucional one-page com abas de rolagem dinamicas e painel de administracao exclusivo para cada instalacao do site.

## Stack
- PHP 8+
- JSON local para persistencia (`data/site.json`)
- CSS/JS puros

## Como rodar localmente
1. Entre na pasta do projeto.
2. Rode:
   ```bash
   php -S localhost:8000
   ```
3. Acesse `http://localhost:8000`
4. Dashboard: `http://localhost:8000/admin/login.php`

## Acesso inicial do dashboard
- Usuario: `admin`
- Senha: `admin123`

No primeiro login bem-sucedido, a senha passa a ser armazenada com hash.

## O que pode editar no dashboard
- Dados da empresa (nome, titulo, intro, telefone, email, endereco, whatsapp)
- Exibicao da marca no topo: somente nome, somente logo ou ambos
- Cores principais do site
- Criacao de novas abas (secao)
- Ordem das abas no menu
- Conteudo, slug e nome do menu de cada aba
- Cor de fundo da aba
- Upload/remocao de imagem de fundo por aba
- Fundo dos quadros internos por aba (padrao / acompanhar cor / personalizado)
- Fonte/tamanho/cor de titulos e textos por aba
- Funcoes por aba (texto, midia lateral, midia de fundo, cards, galeria, YouTube, mapa, formulario, carousel)
- Usuario e senha do painel

## Publicacao em hospedagem
- Hospedagem deve suportar PHP 8+.
- Envie todos os arquivos via FTP/Git.
- Garanta permissao de escrita em `data/` e `uploads/`.
- Defina uma senha forte para o dashboard apos publicar.

## Limite de upload de imagens
- Formatos aceitos:
  - Logo: `JPG`, `PNG`, `WEBP`, `SVG`
  - Fundo de aba: `JPG`, `PNG`, `WEBP`
- Tamanho maximo atual:
  - O projeto **nao define um limite fixo no codigo**.
  - O limite real vem do PHP (`php.ini`), principalmente:
    - `upload_max_filesize`
    - `post_max_size`
- Regra pratica:
  - O arquivo deve ser menor ou igual ao menor valor entre `upload_max_filesize` e `post_max_size`.
- Recomendacao para performance:
  - Logo: ate `1 MB`
  - Fundo de aba: ate `3 MB`

## Documentacao de fluxo
- Status completo desta fase: `docs/STATUS_E_BACKLOG_2026-02-13.md`
- Status rapido (raiz): `STATUS.md`
- Handoff mais recente: `docs/HANDOFF_2026-02-13.md`
- Status atual: `docs/STATUS_ATUAL.md`
- Diario de projeto: `docs/DIARIO_DE_PROJETO.md`
- Plano de testes: `docs/PLANO_DE_TESTES.md`
- Conclusao e backlog: `docs/CONCLUSAO_E_BACKLOG.md`
