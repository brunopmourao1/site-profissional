# Plano de Testes (MVP)

## Pre-condicoes
- PHP 8+ instalado.
- Servidor local iniciado com:
  - `php -S localhost:8000`
- Acesso as rotas:
  - Site: `http://localhost:8000`
  - Dashboard: `http://localhost:8000/admin/login.php`

## 1. Testes de autenticacao
- [ ] Login com credencial padrao (`admin` / `admin123`).
- [ ] Login com senha incorreta deve falhar.
- [ ] Acesso direto a `admin/index.php` sem login deve redirecionar para login.
- [ ] Logout encerra sessao e bloqueia acesso ao painel.
- [ ] Alterar usuario/senha no painel e validar novo acesso.

## 2. Testes de dados gerais
- [ ] Alterar nome da empresa e validar no topo do site.
- [ ] Alterar headline/intro e validar na hero.
- [ ] Alterar telefone/email/endereco e validar no rodape.
- [ ] Alterar cores primaria/secundaria e validar impacto visual.
- [ ] Validar botao WhatsApp com numero configurado.
- [ ] Marca no topo:
  - [ ] somente nome
  - [ ] somente logo
  - [ ] ambos
  - [ ] clique leva para `#inicio`

## 3. Testes de secoes (abas) - CRUD
- [ ] Criar nova secao e validar:
  - aparece no menu
  - aparece na rolagem
- [ ] Editar titulo, conteudo, slug e menu_label.
- [ ] Reordenar secao (subir/descer) e validar ordem no menu e no conteudo.
- [ ] Excluir secao e validar remocao total.
- [ ] Salvar uma aba deve manter o painel ativo (nao voltar para "Dados gerais").

## 4. Testes de uploads (imagens e videos)
- [ ] Upload de imagem JPG em secao.
- [ ] Upload de imagem PNG em secao.
- [ ] Upload de imagem WEBP em secao.
- [ ] Upload de video MP4 em hero e em secao.
- [ ] Tentar formato invalido (ex: PDF) e validar bloqueio.
- [ ] Validar limite de tamanho conforme `php.ini`:
  - `upload_max_filesize`
  - `post_max_size`
- [ ] Remover imagem/video e validar fallback (cor de fundo ou modo texto).

## 5. Tipografia e cores por secao
- [ ] Alterar `text_color` da secao e validar:
  - titulo
  - descricao/conteudo
  - caixas internas (mapa, youtube, formulario)
- [ ] Alterar fontes e tamanhos (titulo + descricao) e validar em:
  - texto normal
  - cards
  - galeria logos
  - formulario (titulo do formulario e labels)
- [ ] Fundo dos quadros internos:
  - [ ] `default`
  - [ ] `section`
  - [ ] `custom`

## 6. Testes por funcao de secao (1 a 10)
- [ ] 1) `basic_text`
- [ ] 2) `split_media` (imagem)
- [ ] 2) `split_media` (video)
- [ ] 3) `background_media` (imagem)
- [ ] 3) `background_media` (video)
- [ ] 4) `cards_media` (1 a 8 cards)
- [ ] 5) `cards_text` (1 a 8 cards)
- [ ] 6) `linked_gallery` (mosaic 2 a 6)
- [ ] 6) `linked_gallery` (logos 2 a 18)
- [ ] 7) `youtube` (URL e ID)
- [ ] 8) `map`:
  - [ ] iframe src (embed) deve funcionar
  - [ ] link curto (maps.app.goo.gl) deve ser rejeitado/limpo
- [ ] 9) `contact_form` (somente aparece com e-mail valido)
- [ ] 10) `carousel`:
  - [ ] 2 a 5 itens
  - [ ] itens com imagem
  - [ ] itens com video
  - [ ] layout `boxed`
  - [ ] layout `full`
  - [ ] links por slide (overlay "Acessar")

## 7. Responsividade e UX
- [ ] Menu mobile abre/fecha corretamente.
- [ ] Links de menu rolam para secoes corretas.
- [ ] Layout legivel em desktop.
- [ ] Layout legivel em celular.
- [ ] Animacoes de entrada nao quebram navegacao.

## 8. Persistencia
- [ ] Fazer alteracoes no dashboard.
- [ ] Reiniciar servidor local.
- [ ] Confirmar que dados permanecem em `data/site.json`.

## 9. Testes para publicacao
- [ ] Confirmar permissao de escrita em `data/` e `uploads/`.
- [ ] Alterar credencial padrao antes de producao.
- [ ] Validar site em dominio final.
- [ ] Validar envio de imagens e videos no ambiente de hospedagem.

## Resultado esperado para concluir projeto
- Todos os checkboxes acima marcados.
- Sem erro critico de navegacao, edicao ou autenticacao.

