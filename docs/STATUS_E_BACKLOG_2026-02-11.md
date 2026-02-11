# STATUS E BACKLOG - site-profissional

Data de referencia: 2026-02-11  
Projeto: `c:\projetos\site-profissional`

## 1. Objetivo deste documento
Registrar com clareza:
- tudo que foi implementado ate agora
- estado atual do projeto
- o que ainda precisa ser feito
- qual e a ordem recomendada para concluir

## 2. Snapshot atual do projeto
- Site: `http://localhost:8000`
- Dashboard: `http://localhost:8000/admin/login.php`
- Branch local: `main`
- Remote: `origin -> https://github.com/brunopmourao1/site-profissional.git`
- Persistencia: `data/site.json`
- Uploads: pasta `uploads/`

Estado atual dos dados (snapshot rapido):
- `site_name`: `Creative Box`
- `headline`: `CREATIVE BOX`
- `hero_mode`: `text`
- secoes cadastradas no momento: `1`
- secao atual: `Quem Somos` (`section_function=cards_media`)

## 3. O que ja foi implementado

### 3.1 Base do sistema
- Site institucional one-page com menu dinamico por secoes.
- Dashboard administrativo com login e sessao.
- Dados persistidos em JSON local.
- Upload de arquivos para `uploads/` com remocao quando necessario.

### 3.2 Dashboard (dados gerais)
- Configuracao de nome da empresa, titulo principal e textos.
- Upload de logo em imagem com texto alternativo.
- Cores principais do site.
- Contatos e rodape (telefone, email, endereco, whatsapp).
- Redes sociais no rodape com ativacao individual por checkbox e campo de link.

### 3.3 Aba principal (hero)
- Funcoes disponiveis:
  1. Texto
  2. Video
  3. Imagem
- Variantes de midia:
  - fundo com texto
  - lateral com texto
  - somente midia (full)
- Configuracoes de midia lateral:
  - posicao esquerda/direita
  - tamanho pequeno/medio/grande
  - largura em px
  - altura em px
  - encaixe cover/contain
- Cor do texto principal configuravel.
- Texto de apoio (overline) configuravel.

### 3.4 Secoes (abas) dinamicas
- Funcoes disponiveis por aba:
  1. titulo + texto
  2. texto + imagem/video lateral
  3. texto + imagem/video de fundo
  4. cards com texto e imagem
  5. cards com titulo e texto
  6. galeria de imagens com links
  7. video do YouTube
  8. mapa do Google Maps (embed)
  9. formulario de contato
- Cada secao pode ser criada, editada, reordenada e removida.

### 3.5 Cards e galeria (parte mais recente)
- Limite configuravel por aba para cards.
- Limite configuravel por aba para galeria com links.
- Cadastro e edicao individual dos cards existentes.
- Cadastro em lote de novos cards.
- Cadastro e edicao individual das imagens com link.
- Cadastro em lote de novas imagens com link.
- Novo ajuste recente:
  - limite maximo de cards agora e `8`
  - layout para 4 cards por linha em desktop (4+4 quando houver 8)
  - opcao de espacamento:
    - `spaced` (com espaco)
    - `compact` (cards juntos, mesma largura)
- Novo ajuste recente:
  - novos cards no dashboard aparecem em blocos separados (`Novo card #1`, `#2`, ...), melhorando usabilidade.

### 3.6 Tipografia e formatacao
- Selecao de fonte e tamanho para diversos textos.
- Ferramentas basicas de rich text para conteudo de secao:
  - negrito
  - italico
  - sublinhado
  - lista

### 3.7 Animacoes
- Animacoes globais configuraveis:
  - slide
  - fade
  - zoom
  - lift
  - esquerda
  - direita
  - cima
  - letras
  - sem animacao
- Movimento do hero configuravel (suave/dramatico/sem).

### 3.8 Navegacao e UX
- Marca/logo no topo com link para `#inicio`.
- Sidebar do dashboard refeita em estilo mais profissional.
- Breadcrumb de painel ativo no topo.
- Estrutura de paineis com organizacao em blocos.

## 4. Testes e validacoes executadas
- Lint de PHP validado (sem erro de sintaxe):
  - `admin/index.php`
  - `includes/site_data.php`
  - `index.php`
- Testes manuais feitos ao longo das iteracoes:
  - login/logout
  - persistencia apos salvar e relogar
  - CRUD de secoes
  - upload de imagem e video
  - links e whatsapp
  - variacoes visuais de hero e secoes

## 5. O que ainda falta (backlog)

### Prioridade alta
1. Adicionar botao de minimizar/expandir por card no dashboard.
2. Rodar QA manual completo em todas as funcoes de secao (1 a 9).
3. Validar comportamento em mobile para todos os modos de midia e cards.

### Prioridade media
1. Revisar visual fino de espacamentos e hierarquia tipografica.
2. Revisar acessibilidade basica (contraste, foco visivel, labels).
3. Revisar mensagens de erro/sucesso para orientar melhor o usuario final.

### Prioridade baixa
1. Melhorar documentacao de deploy por tipo de hospedagem.
2. Avaliar migracao futura de JSON para banco se crescer uso simultaneo.

## 6. Pontos de atencao conhecidos
- O formulario de contato por secao ainda segue fluxo simples (mailto/abordagem leve), sem backend SMTP dedicado.
- Limites reais de upload dependem de `php.ini` no ambiente:
  - `upload_max_filesize`
  - `post_max_size`
- Sempre validar permissao de escrita em `data/` e `uploads/` no servidor.

## 7. Plano de conclusao recomendado
1. Fechar item de minimizar/expandir por card.
2. Rodar checklist completo de QA funcional.
3. Congelar layout e revisar responsividade.
4. Fazer commit final de release.
5. Publicar em hospedagem de homologacao.
6. Validar novamente em ambiente real.

## 8. Comandos de retomada rapida
```powershell
cd C:\projetos\site-profissional
php -S localhost:8000
```

Git (quando for gravar esta versao):
```powershell
git add .
git commit -m "docs: status completo e backlog atualizado"
git push origin main
```

