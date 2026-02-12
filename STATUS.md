# STATUS - site-profissional

Data de referencia: 2026-02-12
Projeto: `c:\projetos\site-profissional`

## Onde estamos
- Plataforma funcional com dashboard completo e site one-page dinamico.
- Menu do topo configuravel para exibir: somente nome, somente logo ou logo + nome.
- Hero com funcoes de texto/video/imagem e variacoes de layout.
- Secoes com 9 funcoes disponiveis (texto, midia lateral, midia de fundo, cards, galeria, YouTube, mapa, formulario).
- Cards com limite maximo de 8, layout 4x2 em desktop e opcao de espacamento (`spaced`/`compact`).
- Cadastro de novos cards em blocos separados no dashboard para facilitar edicao.

## Documento principal desta fase
- `docs/STATUS_E_BACKLOG_2026-02-11.md`

## Pendencias principais
1. Rodar QA manual completo em todas as funcoes de secao (1 a 9).
2. Validar comportamento em mobile para todos os modos de midia e cards.

## Como rodar local
```powershell
cd c:\projetos\site-profissional
php -S localhost:8000
```

## Enderecos locais
- Site: `http://localhost:8000`
- Dashboard: `http://localhost:8000/admin/login.php`
