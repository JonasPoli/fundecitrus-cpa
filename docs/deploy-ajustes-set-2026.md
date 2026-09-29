# Publicação — ajustes set/2026

1. Backup do banco de produção.
2. `git pull` da branch `ajustes-set-2026` e `composer install --no-dev -o`.
3. `php bin/console doctrine:migrations:migrate` (migration `Version20260929180000`).
4. Simular a carga de conteúdo: `php bin/console app:cpa:sync-conteudo --dry-run`
5. Aplicar: `php bin/console app:cpa:sync-conteudo`
   - Cadastra 29 instituições (com país, cidade e coordenadas), 77 pesquisadores, 2 grandes áreas, 5 linhas e 12 módulos, e os 2 banners da home.
   - Remove os parceiros e pesquisadores que não estão nas listas do cliente e os 3 projetos de exemplo (projetos com documentos vinculados são mantidos).
   - Pode ser rodado de novo sem duplicar nada.
6. `./build.sh` (cache, Tailwind do admin e asset map).
7. Logos do rodapé: gerenciados no admin em **Logos do Rodapé** (categorias e empresas com logo, nome e URL).
   A migration `Version20260929201500` já cria Financiadores (Esalq/USP, FAPESP, Fundecitrus) e Apoio (Fealq);
   basta enviar os logos. Empresa sem logo aparece com o nome em texto.
8. Conferir se `var/uploads/registrations` pode ser gravado pelo PHP (arquivos das inscrições, fora da pasta pública).
