# Publicação — ajustes out/2026

1. Backup do banco de produção.
2. `git pull` e `./build.sh` (cache, Tailwind do admin e asset map). Não há dependências novas no Composer.
3. `php bin/console doctrine:migrations:migrate` (migration `Version20261009150000`):
   - `researcher.category` (todos os atuais viram "Pesquisador");
   - `research_line.image_id` e `research_line.area_id` opcional (Grandes Áreas saem do site e do menu do admin);
   - tabela `governance_member`, já preenchida com os integrantes enviados pelo cliente.
4. Não rodar mais o `app:cpa:sync-conteudo`: ele recria a estrutura antiga de linhas de pesquisa.

## Conteúdo a ajustar no admin depois do deploy

- **Banners da Home**
  - Banner 1: botão "Conheça o centro" / "Discover the center", link `/pt/sobre`.
  - Banner 2: título "Pesquisa e desenvolvimento para a inovação e sustentabilidade da citricultura" /
    "Research and development for innovation and sustainability in citriculture";
    botão "Nossas linhas de pesquisa" / "Our research lines", link `/pt/pesquisa#linhas`.
- **Logos do Rodapé**: reenviar FAPESP e USP com os arquivos recortados (sem margem transparente).
- **Instituições Parceiras**: cadastrar Apta Regional de Colina (Colina, SP) e PUC Paraná (Curitiba, PR).
- **Linhas e Módulos**
  - Renomear "Educação e difusão do conhecimento" para "Educação, difusão do conhecimento e transferência de tecnologia"
    e criar nela os módulos "Educação e difusão do conhecimento" e "Transferência de tecnologia".
  - Excluir a linha "Transferência de tecnologia" (sem módulos nem projetos).
  - Enviar a imagem de cada linha e conferir a ordem arrastando na listagem.
- **Projetos Científicos**: cadastrar cada projeto escolhendo o Módulo e o Pesquisador Responsável.
  Os 3 projetos de exemplo antigos não têm módulo e podem ser excluídos.
- **Equipe**: definir a categoria (pesquisador, bolsista ou apoio técnico) e enviar as fotos.
- **Governança**: conferir os itens e, se quiserem, enviar os logos de Fapesp e Fundecitrus em "Mantenedores".
