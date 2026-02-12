# Relatorio de Testes - 2026-02-11

## Ambiente
- Projeto: `c:\projetos\site-profissional`
- PHP: 8.3.30
- upload_max_filesize: 2M
- post_max_size: 8M

## Testes automatizados executados
1. Lint de todos os arquivos PHP (`php -l`) - PASS
2. Validacao de `data/site.json` - PASS
3. Smoke test HTTP `index.php` - PASS (200)
4. Smoke test HTTP `admin/login.php` - PASS (200)
5. Protecao de `admin/index.php` sem sessao - PASS (302 para login)
6. Login invalido retorna erro - PASS
7. Assets principais (`assets/site.css`, `assets/site.js`) - PASS (200)
8. Uploads de imagem e video publicados - PASS (200)
9. Link da marca para `index.php#inicio` - PASS
10. Menu dinamico por secoes renderizado - PASS
11. Hero com largura/altura customizadas em px renderiza variaveis CSS - PASS

## Ajuste validado hoje
- Correcao aplicada para largura/altura customizadas em px na midia de hero e layout lateral:
  - `index.php`
  - `assets/site.css`
- A configuracao em px agora e respeitada sem ser limitada pelo `max-height` padrao.

## Pendencias de teste manual (recomendado)
1. Login valido com credenciais finais (apos troca de senha de producao).
2. Fluxo completo de salvar em cada painel do dashboard (dados gerais + secoes).
3. Confirmar em cada aba lateral (split-left/split-right) os campos:
   - `Largura customizada da midia (px)`
   - `Altura customizada da midia (px)`
4. Responsividade (desktop + mobile) e animacoes no scroll.
5. Upload de arquivos maiores para validar limites reais da hospedagem.

## Observacao de seguranca
- A credencial padrao `admin/admin123` ainda esta valida no estado atual.
- Antes de publicar, alterar usuario/senha no painel.
