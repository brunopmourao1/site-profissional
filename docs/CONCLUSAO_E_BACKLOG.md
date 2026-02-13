# Conclusao e Backlog

## Checklist de conclusao (v1.0)
- [ ] Executar `docs/PLANO_DE_TESTES.md` por completo.
- [ ] Corrigir bugs encontrados.
- [ ] Inserir conteudo real da empresa.
- [ ] Substituir imagens/videos provisarios por assets finais.
- [ ] Trocar credenciais padrao do dashboard.
- [ ] Publicar em hospedagem com PHP 8+.
- [ ] Validar funcionamento em producao (desktop e mobile).

## Backlog recomendado (v1.1+)
- [ ] Protecao CSRF em formularios do dashboard.
- [ ] Limite de tamanho de upload e validacao MIME mais rigida (principalmente em hospedagem).
- [ ] Logs de auditoria (quem alterou o que e quando).
- [ ] Backup automatico/versionado de `data/site.json`.
- [ ] Suporte a multiplos admins.
- [ ] Melhorias de SEO tecnico (meta tags, Open Graph, sitemap).
- [ ] Formulario com envio real (SMTP/API) para nao depender do `mailto:`.

## Criterio de pronto
- O visitante consegue:
  - entender quem e a empresa,
  - navegar por todas as secoes,
  - acessar contatos facilmente,
  - ver midias (imagens/videos/carousel) sem falhas.
- O admin consegue editar:
  - textos, fontes e cores,
  - fundos (aba e quadros internos),
  - cards/galerias/carousel,
  - e ativar/desativar recursos sem mexer em codigo.

