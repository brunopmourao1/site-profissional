# STATUS E BACKLOG - site-profissional

Data de referencia: 2026-02-13
Projeto: `c:\projetos\site-profissional`

## 1. Objetivo deste documento
Registrar com clareza:
- o que foi implementado ate agora
- o estado atual do projeto
- o que ainda precisa ser feito
- a ordem recomendada para concluir

## 2. Snapshot do projeto
- Site: `http://localhost:8000`
- Dashboard: `http://localhost:8000/admin/login.php`
- Persistencia: `data/site.json`
- Uploads: `uploads/`

## 3. Visao geral (arquitetura)
- PHP puro (sem framework) para facilitar hospedagem simples.
- Conteudo e configuracoes salvos em JSON local (`data/site.json`).
- Dashboard administra dados gerais e secoes.
- Site renderiza tudo em `index.php` (one-page com menu por secoes).
- CSS/JS em `assets/` (sem build step).

Arquivos chave:
- `index.php` (frontend)
- `admin/login.php`, `admin/index.php`, `admin/logout.php` (dashboard)
- `includes/site_data.php` (defaults, normalizacao e helpers)
- `assets/site.css`, `assets/site.js` (visual e interacoes)
- `assets/admin.css`, `assets/dashboard-ui.js` (dashboard)

## 4. O que ja foi implementado

### 4.1 Autenticacao e seguranca basica
- Login/sessao para acesso ao dashboard.
- Redirecionamento para `admin/login.php` quando nao autenticado.
- Alteracao de usuario/senha no painel.

### 4.2 Dados gerais do site (dashboard)
- Nome da empresa / marca:
  - Texto configuravel (fonte e tamanho).
  - Logo em imagem (upload + alt).
  - Opcao de exibicao: somente texto, somente logo, ou ambos.
  - Clique na marca/logo leva para `#inicio`.
- Cores globais:
  - cor primaria e secundaria.
- Hero (aba principal/topo):
  - modos: texto, imagem, video
  - variantes: fundo com texto, lateral com texto, somente midia (full)
  - configuracoes da midia lateral:
    - esquerda/direita
    - tamanho (pequeno/medio/grande)
    - fit (cover/contain)
    - largura (px) e altura (px)
  - overline (texto acima do titulo) configuravel
  - cor do texto do hero configuravel
- Rodape:
  - telefone, e-mail, endereco, whatsapp
  - fonte e tamanho configuraveis para os contatos do rodape
  - redes sociais com checkbox individual + campo de link:
    - Facebook, Instagram, LinkedIn, Behance, YouTube
    - WhatsApp entra como ultimo icone quando existir numero configurado

### 4.3 Secoes (abas) dinamicas
CRUD completo:
- criar/editar/remover secao
- subir/descer ordem
- menu dinamico (cada secao vira item no menu)

Campos por secao:
- titulo, nome no menu, slug
- cor de fundo da aba + imagem de fundo opcional (upload/remover)
- fonte e tamanho para titulo e texto
- cor do texto da secao
- animacao por secao (pode herdar do geral)

#### 4.3.1 Fundo dos "quadros internos" (novo)
Cada secao possui "Fundo dos quadros internos", que controla o fundo de:
- painel interno (o quadro central / `.overlay-content`)
- caixas internas (mapa, youtube, formulario, etc)
- cards (quando aplicavel)

Modos:
- `default`: visual claro padrao do site
- `section`: acompanhar a cor da aba
- `custom`: cor personalizada por secao

Chaves no JSON da secao:
- `frame_bg_mode`: `default|section|custom`
- `frame_bg_color`: `#rrggbb` (quando `custom`)

### 4.4 Funcoes das secoes (aba "Funcoes da aba")
Cada secao escolhe 1 funcao principal (`section_function`) e 1 feature extra (`section_feature`).

Funcoes disponiveis (`section_function`):
1. `basic_text`: titulo + texto (fundo por cor)
2. `split_media`: titulo + texto com imagem/video lateral
3. `background_media`: titulo + texto com imagem/video no fundo
4. `cards_media`: cards com imagem + texto
5. `cards_text`: cards com titulo + texto (sem imagem)
6. `linked_gallery`: imagens com links
7. `youtube`: video do YouTube (embed)
8. `map`: Google Maps (embed)
9. `contact_form`: formulario de contato (mailto)
10. `carousel`: carousel (2 a 5 midias, imagem ou video)

Feature extra (`section_feature`):
- `none`, `youtube`, `map`, `contact_form`, `linked_gallery`, `cards`, `carousel`

### 4.5 Cards (funcao 4 e 5)
Frontend:
- ate 8 cards por secao (layout 4 por linha no desktop; 5 fica 4 em cima + 1 centralizado embaixo).
- opcao de espacamento:
  - `spaced` (com espaco)
  - `compact` (tudo junto, cards encostados)
- cards centralizados na aba independente da quantidade ativa.

Dashboard:
- configuracao de quantidade maxima de cards por secao (1 a 8).
- cards existentes em blocos individuais.
- novos cards aparecem como blocos separados para facilitar a leitura.
- cada card tem minimizar/expandir (para nao poluir a tela).

### 4.6 Galeria de imagens com links (funcao 6)
Layouts:
- `mosaic`: 2 a 6 imagens preenchendo a aba em grid (mosaico), com hover (opacidade + botao).
- `logos`: 2 a 18 imagens com links (estilo lista de logos), com titulo + descricao no topo.

Observacao:
- o limite maximo depende do layout:
  - `mosaic`: max 6
  - `logos`: max 18

### 4.7 Carousel (funcao 10)
- 2 a 5 itens por secao (`carousel_items`).
- cada item pode ser imagem ou video.
- opcao de layout:
  - `boxed`: dentro do conteudo com titulo/descricao
  - `full`: carousel preenche a aba inteira
- link opcional por slide com overlay "Acessar".

### 4.8 YouTube (funcao 7)
- campo aceita URL completa ou ID e converte para embed automaticamente.

### 4.9 Google Maps (funcao 8)
- campo salva apenas URL embutivel (embed).
- regra pratica:
  - cole o `src` do iframe do Google Maps (URL com `/maps/embed`).
- URLs curtas (ex: `maps.app.goo.gl/...`) nao sao embutidas e sao rejeitadas.

### 4.10 Formulario de contato (funcao 9)
- formulario por secao, com:
  - titulo do formulario
  - e-mail de destino (fallback para e-mail geral quando definido)
  - texto do botao
- envio atual via `mailto:` (nao existe backend SMTP).

### 4.11 Tipografia, cores e heranca (detalhe importante)
- `text_font`, `text_size` e `text_color` sao aplicados no container da secao para a maior parte do conteudo.
- titulos (`h2` da secao e `h3` de cards/form) usam `title_font` e `title_size`.
- "Fundo dos quadros internos" aplica `--frame-bg` e `--subframe-bg` no CSS para manter consistencia visual.

### 4.12 Animacoes
- animacao global configuravel (entrada das secoes).
- animacao por secao:
  - pode herdar do geral
  - ou escolher estilos: slide, fade, zoom, lift, esquerda, direita, cima, letras, none

## 5. Limites e formatos (uploads)
Imagens aceitas:
- logo: `.jpg`, `.jpeg`, `.png`, `.webp`, `.svg`
- fundos e midias das secoes: `.jpg`, `.jpeg`, `.png`, `.webp`

Videos aceitos:
- hero e secoes: `.mp4`, `.webm`, `.ogg`
- carousel: `.mp4`, `.webm`, `.ogg`

Limite de tamanho:
- o projeto nao define limite fixo no codigo.
- o limite real vem do PHP (`php.ini`), principalmente:
  - `upload_max_filesize`
  - `post_max_size`

Validacao:
- quando disponivel, o dashboard usa `fileinfo` para detectar MIME.
- quando nao disponivel, valida por extensao do arquivo.

## 6. Testes (estado atual)
Ja validado:
- `php -l` sem erros em:
  - `index.php`
  - `admin/index.php`
  - `includes/site_data.php`
- testes manuais de CRUD, login/logout, uploads e navegacao foram realizados durante a construcao.

Ainda falta (prioridade alta):
- QA manual completo com todas as funcoes (1 a 10) em desktop e mobile.

## 7. Backlog recomendado

### Prioridade alta
1. Rodar `docs/PLANO_DE_TESTES.md` completo (atualizar evidencias).
2. Validar responsividade de todas as funcoes em mobile.
3. Validar contraste quando `text_color` for claro em fundo claro.

### Prioridade media
1. Melhorar acessibilidade basica (foco, aria, contraste).
2. Padronizar mensagens de erro e ajuda no dashboard.
3. Ajustar estilos de inputs do formulario para herdarem melhor cor/fonte (se necessario).

### Prioridade baixa
1. CSRF nos formularios do dashboard.
2. Auditoria/log de alteracoes.
3. Backup automatico do `data/site.json`.
4. Opcional: migrar formulario `mailto:` para envio real (SMTP/API) se quiser um contato profissional sem depender do cliente de e-mail do visitante.

## 8. Como retomar rapido
Subir servidor local:
```powershell
cd C:\projetos\site-profissional
php -S localhost:8000
```

Lint (rapido):
```powershell
php -l index.php
php -l admin\index.php
php -l includes\site_data.php
```

## 9. Commit sugerido (quando for gravar esta fase)
```powershell
git add .
git commit -m "docs: status e backlog 2026-02-13"
git push origin main
```

