# Plano de Testes (MVP)

## Pre-condicoes
- PHP 8+ instalado.
- Servidor local iniciado com:
  - `php -S localhost:8000`
- Acesso às rotas:
  - Site: `http://localhost:8000`
  - Dashboard: `http://localhost:8000/admin/login.php`

## 1. Testes de autenticacao
- [ ] Login com credencial padrão (`admin` / `admin123`).
- [ ] Login com senha incorreta deve falhar.
- [ ] Acesso direto a `admin/index.php` sem login deve redirecionar para login.
- [ ] Logout encerra sessão e bloqueia acesso ao painel.
- [ ] Alterar usuário/senha no painel e validar novo acesso.

## 2. Testes de dados globais
- [ ] Alterar nome da empresa e validar no topo do site.
- [ ] Alterar headline/intro e validar na hero.
- [ ] Alterar telefone/email/endereço e validar no rodapé.
- [ ] Alterar cores primária/secundária e validar impacto visual.
- [ ] Validar botão WhatsApp com número configurado.

## 3. Testes de seções (abas)
- [ ] Criar nova seção e validar:
  - aparece no menu.
  - aparece na rolagem.
- [ ] Editar título, conteúdo, slug e menu_label.
- [ ] Reordenar seção (subir/descer) e validar ordem no menu e no conteúdo.
- [ ] Excluir seção e validar remoção total.

## 4. Testes de imagem/cor de fundo
- [ ] Upload de imagem JPG em seção.
- [ ] Upload de imagem PNG em seção.
- [ ] Upload de imagem WEBP em seção.
- [ ] Tentar formato inválido (ex: PDF) e validar bloqueio.
- [ ] Validar limite de tamanho conforme `php.ini` (`upload_max_filesize` e `post_max_size`).
- [ ] Remover imagem e validar fallback para cor de fundo.

## 5. Testes de responsividade e UX
- [ ] Menu mobile abre/fecha corretamente.
- [ ] Links de menu rolam para seções corretas.
- [ ] Layout legível em desktop.
- [ ] Layout legível em celular.
- [ ] Animações de entrada não quebram navegação.

## 6. Testes de persistencia
- [ ] Fazer alterações no dashboard.
- [ ] Reiniciar servidor local.
- [ ] Confirmar que dados permanecem em `data/site.json`.

## 7. Testes para publicação
- [ ] Confirmar permissões de escrita em `data/` e `uploads/`.
- [ ] Alterar credencial padrão antes de produção.
- [ ] Validar site em domínio final.
- [ ] Validar envio de imagens no ambiente de hospedagem.

## Resultado esperado para concluir projeto
- Todos os checkboxes acima marcados.
- Sem erro crítico de navegação, edição ou autenticação.
