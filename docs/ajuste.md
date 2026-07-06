# Projetos
https://127.0.0.1:8000/admin/project/3/edit
Em projetos deve ser possível ter o cadastro de documentos que podem ser do tipo artigo, abstract ou dissertação, com upload do pdf, com ano, pesquisador (campo texto), doi.

Na área pública, em https://127.0.0.1:8000/pt/pesquisa, deve ter uma nova lista de documentos, deve ser possível ver a Listagem completa com filtros por ano, tipo (artigo, abstract, dissertação) e pesquisador.
Download direto do PDF ou redirecionamento para o link DOI.


# Clipping
Falta resumo no Clipping, administrável com o textarea, sem editor html, para aparecer em https://127.0.0.1:8000/pt/comunicacao.

# Vídeos
Os vídeos precisam ser cadastados com o código do vídeo, ou seja, o id, que fica na url do vídeo no youtube, titulo e descrição. 
Na área pública, deve ter uma lista dos vídeos em https://127.0.0.1:8000/pt/comunicacao.
# Podcast
Também deve ser possível gerenciar os podcasts como os vídeos do Youtube.

Caso nao tenha nem video nem podcast, mostrar o card:
```
<div class="relative bg-theme-gradient text-white border-0 rounded-[2.5rem] p-12 text-center text-white aspect-[4/3] flex flex-col items-center justify-center shadow-2xl ring-1 ring-slate-900/10 overflow-hidden group">
                    <img src="/images/comunicacao_studio.png" class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
                    
                    <div class="relative z-10">
                        <button class="w-20 h-20 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white transition-all transform group-hover:scale-110 mb-8 border border-white/20 shadow-xl group-hover:bg-primary hover:border-primary-light">
                            <i class="fa-solid fa-play text-2xl ml-1"></i>
                        </button>
                        <h3 class="text-2xl font-bold mb-3">
                            Galeria de Entrevistas
                        </h3>
                        <p class="text-slate-300">
                            Conectando o centro à sociedade.
                        </p>
                        <div class="mt-8 inline-flex px-4 py-1.5 rounded-full bg-white/10 text-xs font-bold uppercase tracking-widest border border-white/5">
                            Em Breve
                        </div>
                    </div>
                </div>
```                


# eventos
Eventos podem ter um campo novo, o link par inscrição (url)

#Oportunidades
Precisa de um campo novo, o PDF. colocar o PDF do edital na área pública.


# logos no rodapé
Esconda essa administração no admin
Não precisa de logos no rodapé