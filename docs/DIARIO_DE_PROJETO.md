# Diario de Projeto

## 2026-02-10 - Base e MVP
- Projeto criado com PHP puro + JSON (sem framework) para facilitar deploy.
- Pastas base: `admin`, `assets`, `includes`, `data`, `uploads`, `docs`.
- Frontend:
  - `index.php` renderizando secoes dinamicas e menu.
- Backend/Dashboard:
  - `admin/login.php`, `admin/index.php`, `admin/logout.php`.
  - persistencia em `data/site.json`.
- Uploads locais em `uploads/`.

## 2026-02-11 - Dashboard e funcoes das abas
- Dashboard reorganizado em paineis e blocos, com sidebar e breadcrumb.
- Secoes passaram a usar `section_function` (funcao unica por vez).
- Funcoes das secoes implementadas (1 a 9):
  - texto, midia lateral, midia de fundo, cards, galeria com links, YouTube, mapa, formulario.
- Ajustes de tipografia (fontes/tamanhos) e animacoes (global e por secao).
- Rodape com redes sociais (checkbox + link) e WhatsApp como ultimo icone quando numero existir.

## 2026-02-12 - Cards e organizacao
- Cards padronizados com limite maximo 8.
- Layout 4 cards por linha (desktop) e centralizacao automatica conforme quantidade ativa.
- Dashboard:
  - cards existentes e novos cards em blocos individuais
  - minimizar/expandir por card para organizar edicao

## 2026-02-13 - Carousel, mapa e estilo dos quadros internos
- Adicionado `carousel` (funcao 10) por secao:
  - 2 a 5 midias (imagem/video), link por slide, layout `boxed` ou `full`.
- Google Maps:
  - normalizacao/validacao de URL embed (rejeita links curtos e tenta corrigir formatos comuns).
- Adicionado "Fundo dos quadros internos" por secao:
  - `default`, `section` (acompanhar cor da aba), `custom` (cor personalizada).
- Ajustes de heranca de fonte/cor:
  - `text_font/text_size/text_color` aplicados no container da secao
  - titulos (secao, cards, formulario) usando `title_font`.

