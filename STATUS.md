# STATUS - site-profissional

Data de referencia: 2026-02-11
Projeto: `c:\projetos\site-profissional`

## Onde estamos
- O dashboard foi reorganizado por caixas e funcoes, com selecao unica por aba.
- A aba principal (inicio) agora tem 3 funcoes: texto, video, imagem.
- O rodape tem icone do WhatsApp junto das redes sociais.
- O frontend renderiza: cards texto/imagem, galeria com links, YouTube, mapa e formulario de contato por aba.
- Midia lateral (imagem/video) com tamanho por preset (pequeno/medio/grande) e agora tambem por largura/altura em px.

## Snapshot atual dos dados
- `site_name`: `Creative Box`
- `headline`: `CREATIVE BOX DO MEU AMOR`
- `hero_mode`: `image_split`
- Quantidade de abas em `data/site.json`: `1`
- Aba atual: `Quem somos` (`slug`: `quem-somos`, `section_function`: `basic_text`)

## Documentacao detalhada
- Handoff completo: `docs/HANDOFF_2026-02-11.md`

## Como rodar local
1. Abra PowerShell na pasta do projeto:
   `cd c:\projetos\site-profissional`
2. Se `php` estiver no PATH:
   `php -S localhost:8000`
3. Se `php` NAO estiver no PATH, rode com caminho completo:
   `& "C:\CAMINHO\php.exe" -S localhost:8000 -t "c:\projetos\site-profissional"`
4. Acesse:
   - Site: `http://localhost:8000`
   - Dashboard: `http://localhost:8000/admin/login.php`

## Credenciais atuais (se nao alteradas)
- Usuario: `admin`
- Senha: `admin123`

## O que falta fechar
1. Rodar checklist completo de QA manual no site e dashboard.
2. Confirmar comportamento de uploads com limite real do PHP (`upload_max_filesize` e `post_max_size`).
3. Validar fluxo de envio do formulario de contato por aba em navegador real.
4. Fazer ajuste fino visual final (espacamentos e responsividade) apos testes.

## Retomada desta sessao (2026-02-11)
- Projeto revisado com base em `docs/HANDOFF_2026-02-11.md`.
- Sintaxe validada:
  - `php -l index.php`
  - `php -l admin/index.php`
  - `php -l includes/site_data.php`
- Ajustes aplicados no frontend:
  1. Estabilizacao do modo full de video/imagem no hero para evitar faixa visual no topo quando a classe `reveal` interfere.
  2. Videos do site (hero e secoes) com atributos adicionais para reduzir overlays de controle/PiP (`disablepictureinpicture`, `noremoteplayback`, `preload`).
  3. Reforco da logica de animacao para replay ao navegar por ancora:
     - criterio de saida ajustado (`leavePoint = 0`);
     - replay disparado em `hashchange` e clique de menu.
- Smoke test por renderizacao PHP local concluido com sucesso:
  - `index.php` renderizou (`index-bytes=5028`)
  - `admin/login.php` renderizou (`login-bytes=729`)
