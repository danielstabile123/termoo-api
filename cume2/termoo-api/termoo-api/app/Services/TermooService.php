<?php

namespace App\Services;

use Illuminate\Support\Str;

class TermooService
{
    private const TAMANHO_PALAVRA   = 5;
    private const TENTATIVAS_MAX    = 6;
    private const STORAGE_PATH      = 'games'; // dentro de storage/app/

    // -----------------------------------------------------------------------
    // Lista de palavras válidas
    // -----------------------------------------------------------------------
    private array $palavras = [
        'sagaz', 'amago', 'termo', 'negro', 'exito', 'mexer', 'nobre', 'senso', 'etica', 'afeto',
        'algoz', 'fazer', 'plena', 'tenue', 'assim', 'sobre', 'mutua', 'aquem', 'poder', 'secao',
        'vigor', 'porem', 'sutil', 'fosse', 'cerne', 'ideia', 'sanar', 'audaz', 'moral', 'inato',
        'quica', 'muito', 'justo', 'desde', 'honra', 'sonho', 'torpe', 'amigo', 'razao', 'egide',
        'icone', 'etnia', 'futil', 'anexo', 'dengo', 'tange', 'haver', 'lapso', 'entao', 'tempo',
        'expor', 'seara', 'bocal', 'saber', 'habil', 'graca', 'mutuo', 'xibiu', 'casal', 'obice',
        'avido', 'dizer', 'ardil', 'estar', 'pesar', 'dever', 'causa', 'tenaz', 'ainda', 'sendo',
        'temor', 'crivo', 'brado', 'paria', 'coser', 'apice', 'genro', 'comum', 'posse', 'prole',
        'assaz', 'corja', 'animo', 'fugaz', 'ceder', 'pauta', 'censo', 'detem', 'culto', 'ansia',
        'atroz', 'digno', 'mundo', 'forte', 'mesmo', 'vulgo', 'vicio', 'saude', 'gleba', 'criar',
        'todos', 'cozer', 'reves', 'jeito', 'pudor', 'dogma', 'valha', 'denso', 'nenem', 'louco',
        'limbo', 'ordem', 'atras', 'regra', 'pedir', 'homem', 'feliz', 'ajuda', 'clava', 'usura',
        'merce', 'impor', 'banal', 'coisa', 'falso', 'juizo', 'round', 'legal', 'forma', 'falar',
        'sabio', 'certo', 'prosa', 'servo', 'tenro', 'presa', 'pifio', 'posso', 'desse', 'heroi',
        'cunho', 'devir', 'facil', 'viril', 'ontem', 'vendo', 'valor', 'visar', 'linda', 'manso',
        'serio', 'ebrio', 'guisa', 'magoa', 'acaso', 'meiga', 'impio', 'puder', 'fluir', 'afago',
        'lugar', 'reaca', 'abrir', 'temer', 'plato', 'garbo', 'praxe', 'uniao', 'gerar', 'burro',
        'obter', 'matiz', 'obvio', 'cisma', 'bruma', 'venia', 'afins', 'exodo', 'crise', 'pleno',
        'alibi', 'ritmo', 'tedio', 'fluxo', 'morte', 'levar', 'senil', 'olhar', 'casta', 'havia',
        'tomar', 'enfim', 'visao', 'ouvir', 'genio', 'parvo', 'prumo', 'cabal', 'brega', 'parco',
        'reles', 'falta', 'calma', 'vital', 'outro', 'tecer', 'bravo', 'favor', 'pulha', 'terra',
        'reter', 'vivaz', 'viver', 'ameno', 'sabia', 'forca', 'unico', 'tendo', 'laico', 'passo',
        'nicho', 'valia', 'achar', 'grato', 'nossa', 'carma', 'rever', 'papel', 'nocao', 'ranco',
        'pobre', 'possa', 'rogar', 'dubio', 'noite', 'fardo', 'ativo', 'facam', 'prime', 'farsa',
        'coeso', 'epico', 'fator', 'anelo', 'claro', 'leigo', 'lider', 'sesta', 'selar', 'obito',
        'vazio', 'ciume', 'cisao', 'cesta', 'sonso', 'ficar', 'citar', 'deter', 'sinto', 'atuar',
        'velho', 'gente', 'haste', 'fonte', 'adiar', 'ponto', 'tende', 'humor', 'revel', 'ideal',
        'sulco', 'senao', 'arduo', 'labor', 'remir', 'terno', 'igual', 'marco', 'hiato', 'feixe',
        'exato', 'capaz', 'amplo', 'debil', 'tanto', 'lavra', 'relva', 'vemos', 'ciclo', 'cauda',
        'tenra', 'inata', 'jovem', 'raiva', 'chuva', 'varao', 'otica', 'gesto', 'cacar', 'ambos',
        'pouco', 'toada', 'velar', 'sonsa', 'apoio', 'cocar', 'serie', 'vacuo', 'imune', 'xeque',
        'algum', 'farao', 'feito', 'horda', 'vimos', 'carro', 'fusao', 'entre', 'advem', 'sorte',
        'leito', 'coesa', 'probo', 'minha', 'trama', 'cruel', 'sente', 'doido', 'anuir', 'lazer',
        'frase', 'brisa', 'impar', 'verso', 'chata', 'blase', 'rigor', 'massa', 'pegar', 'torco',
        'maior', 'prece', 'botar', 'aurea', 'seita', 'dorso', 'saiba', 'agora', 'signo', 'furor',
        'fauna', 'mocao', 'livro', 'plano', 'liame', 'vetor', 'comer', 'ocaso', 'senda', 'covil',
        'preso', 'credo', 'casto', 'flora', 'morar', 'praia', 'pecha', 'nunca', 'faina', 'alias',
        'docil', 'adeus', 'houve', 'peste', 'ardor', 'mudar', 'arido', 'setor', 'parte', 'manha',
        'ambas', 'peixe', 'risco', 'antro', 'rezar', 'visse', 'pajem', 'estao', 'grupo', 'junto',
        'avaro', 'vulto', 'virus', 'salvo', 'meses', 'campo', 'otimo', 'indio', 'saida', 'beata',
        'breve', 'vasto', 'antes', 'aceso', 'morro', 'conta', 'sinal', 'verbo', 'andar', 'anais',
        'lenda', 'reger', 'oxala', 'aureo', 'banzo', 'prado', 'fugir', 'acima', 'opcao', 'serao',
        'festa', 'vilao', 'chulo', 'rapaz', 'nacao', 'texto', 'segue', 'leite', 'motim', 'birra',
        'fruir', 'tirar', 'treta', 'parar', 'brava', 'bonus', 'fitar', 'atrio', 'idolo', 'puxar',
        'jazia', 'filho', 'turba', 'atomo', 'alude', 'tosco', 'gerir', 'reino', 'tenso', 'prova',
        'prazo', 'traga', 'norma', 'manha', 'exame', 'epoca', 'voraz', 'corpo', 'acesa', 'cheio',
        'sarca', 'ligar', 'preto', 'nosso', 'malta', 'bando', 'aonde', 'magia', 'arcar', 'quase',
        'copia', 'venal', 'fatos', 'logro', 'longe', 'sinha', 'aviao', 'afora', 'psico', 'ancia',
        'fatal', 'dessa', 'certa', 'praga', 'sexta', 'quota', 'nivel', 'fixar', 'oasis', 'mente',
        'glosa', 'messe', 'nodoa', 'apelo', 'lidar', 'apego', 'pompa', 'perda', 'verve', 'parca',
        'tocar', 'alado', 'coito', 'jirau', 'caixa', 'livre', 'fraco', 'sumir', 'porta', 'tinha',
        'vezes', 'grave', 'soldo', 'firme', 'lindo', 'bater', 'canon', 'opaco', 'solto', 'irmao',
        'besta', 'faixa', 'astro', 'salve', 'sabia', 'atual', 'elite', 'turva', 'trupe', 'virao',
        'doido', 'supra', 'navio', 'fenda', 'deixa', 'cioso', 'junco', 'grata', 'alcar', 'pardo',
        'autor', 'curso', 'pique', 'chato', 'bioma', 'parva', 'exijo', 'douto', 'bicho', 'aluno',
        'macio', 'desta', 'pagao', 'viria', 'etico', 'reses', 'menos', 'cousa', 'caber', 'calda',
        'posto', 'ficha', 'radio', 'video', 'culpa', 'abuso', 'locus', 'lapis', 'supor', 'zelar',
        'gosto', 'judeu', 'super', 'suave', 'verba', 'calao', 'advir', 'agudo', 'drops', 'extra',
        'baixo', 'julia', 'molho', 'retem', 'torso', 'prive', 'piada', 'facho', 'sitio', 'ruina',
        'peito', 'vinha', 'vosso', 'turma', 'igneo', 'passa', 'traco', 'podio', 'asilo', 'avida',
        'estio', 'combo', 'pilar', 'light', 'orfao', 'turvo', 'chama', 'mosto', 'pareo', 'museu',
        'acoes', 'louca', 'refem', 'amena', 'poeta', 'lasso', 'acola', 'pisar', 'forem', 'brabo',
        'mesma', 'local', 'medir', 'drama', 'optar', 'busca', 'meigo', 'teste', 'ereto', 'finda',
        'metie', 'poema', 'clima', 'tento', 'aviso', 'cutis', 'folga', 'autos', 'geral', 'coral',
        'surja', 'facto', 'cocho', 'hobby', 'rumor', 'amiga', 'rouca', 'feroz', 'tacha', 'paira',
        'calmo', 'pedra', 'idoso', 'cetro', 'rubro', 'boato', 'pacto', 'volta', 'urgia', 'acude',
        'golpe', 'movel', 'licao', 'feudo', 'crime', 'monge', 'ecoar', 'ateia', 'corso', 'manga',
        'daqui', 'ebano', 'riste', 'clean', 'artur', 'carta', 'casar', 'ponha', 'tetra', 'natal',
        'falha', 'benca', 'monte', 'saldo', 'aroma', 'verde', 'conto', 'escol', 'cacho', 'vetar',
        'itens', 'briga', 'hoste', 'vigia', 'tarde', 'grama', 'tribo', 'plumo', 'forum', 'manga',
        'pasmo', 'ornar', 'letal', 'amada', 'fazia', 'troca', 'vento', 'pedro', 'sucia', 'sosia',
        'chefe', 'unica', 'civil', 'rival', 'fruto', 'uteis', 'nuvem', 'orgao', 'pinho', 'tchau',
        'plaga', 'roupa', 'jogar', 'venha', 'sarau', 'vazao', 'areia', 'jejum', 'atimo', 'plebe',
        'penta', 'berro', 'virar', 'arado', 'nesse', 'swing', 'cargo', 'cover', 'seixo', 'fosso',
        'perto', 'midia', 'catre', 'lesse', 'macro', 'stand', 'magna', 'giria', 'rocha', 'axila',
        'tutor', 'legua', 'beijo', 'varoa', 'bruto', 'todas', 'tiver', 'ticao', 'finjo', 'farta',
        'inter', 'troca', 'calor', 'renda', 'bruta', 'pomar', 'assar', 'tenha', 'tenho', 'traje',
        'gabar', 'deste', 'close', 'santo', 'arfar', 'xucro', 'vadio', 'danca', 'trato', 'surto',
        'estro', 'porte', 'amado', 'nessa', 'ambar', 'guria', 'verao', 'perco', 'silvo', 'logos',
        'rural', 'viram', 'odiar', 'feita', 'chula', 'mamae', 'agape', 'vista', 'aviar', 'cenho',
        'depor', 'laudo', 'nesta', 'canto', 'marca', 'negar', 'vedar', 'etapa', 'bazar', 'fossa',
        'bolsa', 'grota', 'salmo', 'cerca', 'pavor', 'canso', 'minar', 'densa', 'cheia', 'cifra',
        'recem', 'coroa', 'irado', 'urdir', 'regio', 'clero', 'visto', 'quais', 'cinto', 'vagar',
        'letra', 'ferpa', 'burra', 'horto', 'sofia', 'ruido', 'jazer', 'inves', 'esgar', 'bucho',
        'largo', 'folha', 'molde', 'proto', 'segar', 'simio', 'sotao', 'lesao', 'paiol', 'final',
        'pugna', 'ubere', 'trago', 'fundo', 'velha', 'penso', 'lesto', 'farol', 'morfo', 'queda',
        'narco', 'alamo', 'vasta', 'ufano', 'ardis', 'pasma', 'olhos', 'linha', 'ceita', 'troco',
        'podar', 'apear', 'piche', 'deram', 'folia', 'preco', 'audio', 'polis', 'umido', 'bulir',
        'viger', 'troco', 'frota', 'outra', 'mocho', 'neste', 'peita', 'disso', 'chave', 'monta',
        'ileso', 'cosmo', 'matar', 'resto', 'seiva', 'manto', 'chaga', 'redor', 'falsa', 'barro',
        'misto', 'retro', 'mover', 'bolso', 'sacar', 'limpo', 'vazia', 'civel', 'labia', 'bedel',
        'campa', 'louca', 'nariz', 'veloz', 'barao', 'nacar', 'louro', 'samba', 'logia', 'sabor',
        'justa', 'toque', 'mimar', 'lutar', 'album', 'dados', 'banto', 'lucro', 'macho', 'gemer',
        'zumbi', 'axial', 'longo', 'coevo', 'porca', 'punha', 'pagar', 'arroz', 'rente', 'diabo',
        'enjoo', 'calca', 'subir', 'salva', 'farto', 'urgir', 'findo', 'lousa', 'xampu', 'calvo',
        'venho', 'valer', 'baixa', 'pluma', 'focar', 'ousar', 'fatuo', 'sabia', 'bruxa', 'sexto',
        'hifen', 'firma', 'repor', 'sigla', 'pular', 'torna', 'forro', 'lento', 'cardo', 'solta',
        'choca', 'corte', 'bugre', 'gueto', 'feira', 'reler', 'custo', 'fugiu', 'tenis', 'corar',
        'fazes', 'vario', 'mania', 'nesga', 'sadio', 'demao', 'canil', 'racio', 'ferir', 'versa',
        'modal', 'harem', 'socio', 'miope', 'puido', 'sugar', 'digna', 'ceifa', 'tumba', 'patio',
        'abaco', 'abada', 'abade', 'abafa', 'abafo', 'abalo', 'abano', 'abate', 'abece', 'abono',
        'abril', 'acaju', 'acaro', 'aceno', 'acido', 'acuar', 'adaga', 'adega', 'adido', 'adobe',
        'adubo', 'aedes', 'aerar', 'aereo', 'afear', 'afega', 'afiar', 'afogo', 'afoxe', 'agata',
        'agave', 'agito', 'agogo', 'agora', 'aguar', 'aguas', 'aguca', 'aguia', 'aipim', 'aipos',
        'aldea', 'alema', 'aliar', 'altar', 'altos', 'amapa', 'ameba', 'amido', 'amina', 'amino',
        'amora', 'andas', 'anexa', 'anglo', 'angra', 'anima', 'anime', 'anion', 'anodo', 'anual',
        'anzol', 'aorta', 'apaga', 'apara', 'apito', 'apolo', 'apuro', 'arabe', 'arame', 'arara',
        'arder', 'arear', 'areca', 'arena', 'arnes', 'arpao', 'artes', 'aruba', 'ataca', 'atado',
        'atear', 'atica', 'atico', 'ativa', 'atlas', 'atona', 'atono', 'atriz', 'aveia', 'avela',
        'avena', 'azara', 'azeda', 'azedo', 'babao', 'babar', 'babau', 'babel', 'bacia', 'bacon',
        'baeta', 'bafio', 'bagre', 'baiao', 'baila', 'baile', 'baita', 'balao', 'balar', 'balde',
        'balsa', 'bamba', 'bambo', 'bambu', 'banca', 'banco', 'banda', 'banho', 'banir', 'banjo',
        'bantu', 'baque', 'barba', 'barca', 'barco', 'barda', 'barra', 'basto', 'batel', 'batom',
        'beato', 'beber', 'bebes', 'bebum', 'beija', 'beira', 'belga', 'bemol', 'bento', 'beque',
        'berco', 'berra', 'biela', 'bilha', 'bingo', 'biota', 'birma', 'bisao', 'bispo', 'blefe',
        'bloco', 'blusa', 'boate', 'bobar', 'bocal', 'bocha', 'bocio', 'boina', 'bolao', 'bolar',
        'bolas', 'boldo', 'bolha', 'bolor', 'bomba', 'bonde', 'borax', 'borda', 'bordo', 'borla',
        'borra', 'boson', 'botao', 'botim', 'botox', 'braca', 'braco', 'braga', 'brama', 'brasa',
        'brita', 'broca', 'broto', 'broxa', 'bruxo', 'bucal', 'bucha', 'bufao', 'bufar', 'bugio',
        'bujao', 'bulbo', 'buque', 'buque', 'buril', 'busto', 'butim', 'buzio', 'cabra', 'cacao',
        'cacau', 'cacto', 'caiar', 'caida', 'caido', 'cairo', 'calar', 'calce', 'calco', 'caldo',
        'calha', 'calix', 'calva', 'cambo', 'canal', 'canja', 'canoa', 'cante', 'capao', 'capar',
        'capim', 'capuz', 'caqui', 'carga', 'carie', 'caril', 'carne', 'carne', 'carpa', 'carpo',
        'casao', 'casca', 'casco', 'caspa', 'cassa', 'catar', 'caule', 'causo', 'cauto', 'cavar',
        'cedro', 'cegar', 'celta', 'cento', 'cerar', 'cerco', 'cerda', 'cerva', 'cervo', 'cesto',
        'cetim', 'cevar', 'chale', 'chapa', 'chega', 'chiar', 'chico', 'chile', 'chili', 'china',
        'chino', 'chita', 'choca', 'choco', 'chope', 'chora', 'choro', 'chule', 'chupa', 'chuta',
        'chute', 'ciano', 'cidra', 'cilio', 'cinco', 'cinta', 'cinza', 'circo', 'cirio', 'cisco',
        'cisne', 'cisto', 'clama', 'clara', 'clave', 'clipe', 'clone', 'cloro', 'clube', 'coach',
        'cobra', 'cobre', 'coice', 'coifa', 'coiso', 'colar', 'colmo', 'colon', 'color', 'conde',
        'conga', 'copas', 'coque', 'corca', 'corco', 'corda', 'cores', 'corno', 'corra', 'corre',
        'corro', 'corsa', 'corta', 'corvo', 'costa', 'costo', 'cotar', 'cotas', 'cotia', 'coura',
        'couro', 'couto', 'couve', 'coxim', 'crack', 'cravo', 'crawl', 'creme', 'crepe', 'crina',
        'cromo', 'crono', 'crush', 'cubar', 'cueca', 'cuica', 'curar', 'curia', 'curry', 'curta',
        'curto', 'curva', 'curvo', 'cusco', 'cuspe', 'cuspo', 'custa', 'cutia', 'damas', 'danar',
        'dandi', 'danes', 'dardo', 'datar', 'dedao', 'dedar', 'deita', 'delas', 'delta', 'dente',
        'deque', 'derbi', 'derby', 'derma', 'derme', 'deusa', 'diaba', 'diada', 'diade', 'diana',
        'dieta', 'dinar', 'dinda', 'dingo', 'diodo', 'dique', 'disco', 'disto', 'ditar', 'doado',
        'dobar', 'dobra', 'dobre', 'dobro', 'dodoi', 'doida', 'dolar', 'dolma', 'dolor', 'domar',
        'donde', 'dores', 'dorna', 'dosar', 'dotar', 'draga', 'drink', 'drive', 'droga', 'drone',
        'duble', 'ducal', 'ducha', 'ducto', 'duelo', 'dueto', 'dupla', 'duplo', 'duque', 'durao',
        'durar', 'duzia', 'edipo', 'edito', 'egito', 'ejeto', 'emoji', 'emulo', 'enves', 'envio',
        'epica', 'erbio', 'ergio', 'errar', 'espia', 'esqui', 'esses', 'estai', 'ester', 'estou',
        'etano', 'eteno', 'ethos', 'etila', 'etilo', 'etimo', 'facao', 'fadar', 'falaz', 'falda',
        'falho', 'falir', 'falto', 'falua', 'fanal', 'farda', 'farsi', 'fatao', 'fatia', 'fauce',
        'fauno', 'febre', 'fecal', 'fecha', 'fecho', 'feder', 'fedor', 'felpa', 'femea', 'femeo',
        'femur', 'fenil', 'fenix', 'fenol', 'ferra', 'ferro', 'ferry', 'fetal', 'fezes', 'fiada',
        'fiado', 'fiapo', 'fibra', 'fieis', 'filao', 'filar', 'filha', 'filho', 'filme', 'finar',
        'finca', 'fines', 'finta', 'finto', 'fique', 'fisco', 'fisga', 'flama', 'flame', 'flash',
        'flexo', 'floco', 'fluor', 'flush', 'fobia', 'focal', 'fofao', 'fogao', 'fogos', 'foice',
        'folio', 'fonia', 'forca', 'forja', 'forno', 'forra', 'forro', 'fosca', 'fosco', 'foste',
        'foton', 'fovea', 'foyer', 'fraca', 'frade', 'fraga', 'frear', 'freio', 'fresa', 'frete',
        'frevo', 'frisa', 'friso', 'frita', 'frito', 'front', 'fruta', 'fujao', 'fular', 'fulvo',
        'fumar', 'funda', 'funde', 'funga', 'fungo', 'funil', 'furao', 'furar', 'furia', 'furna',
        'furta', 'furto', 'fusca', 'fusco', 'fuzil', 'gabao', 'gaita', 'galao', 'gales', 'galga',
        'galgo', 'galha', 'galho', 'galio', 'gamao', 'gamar', 'gamba', 'ganga', 'ganho', 'ganir',
        'gansa', 'ganso', 'garca', 'garco', 'garfa', 'garfo', 'garoa', 'garra', 'garua', 'gases',
        'gasto', 'gavea', 'gelar', 'gemeo', 'gesso', 'gesta', 'gibao', 'ginga', 'girar', 'glace',
        'glace', 'globo', 'glote', 'gnose', 'goela', 'golfe', 'golfo', 'gongo', 'gordo', 'gorja',
        'gorro', 'gosma', 'gozar', 'grade', 'grado', 'grafo', 'grana', 'graxa', 'green', 'grega',
        'grego', 'greve', 'grife', 'grifo', 'grill', 'grita', 'grito', 'grude', 'gruta', 'guano',
        'guapo', 'guara', 'guiao', 'guiar', 'guine', 'guita', 'guizo', 'harpa', 'hedge', 'helio',
        'helix', 'hepta', 'herma', 'hertz', 'hidra', 'hidro', 'hiena', 'hindi', 'hindu', 'honor',
        'horas', 'horta', 'hotel', 'hulha', 'humus', 'hurra', 'husky', 'ilheu', 'ilhos', 'iluso',
        'imago', 'imame', 'india', 'infra', 'ingua', 'input', 'iogue', 'iscar', 'islao', 'istmo',
        'itrio', 'jacto', 'janta', 'jante', 'japao', 'jarda', 'jarra', 'jarro', 'jaspe', 'jaula',
        'jeans', 'jegue', 'jeova', 'jeque', 'jesus', 'jetom', 'jihad', 'jogue', 'joias', 'jongo',
        'jorra', 'jorro', 'joule', 'judas', 'judia', 'juiza', 'julho', 'jumbo', 'junca', 'junho',
        'junta', 'jurar', 'kanji', 'karma', 'kebab', 'kendo', 'khmer', 'kraft', 'krill', 'labil',
        'labio', 'lacre', 'ladra', 'ladro', 'lagoa', 'lajem', 'lanca', 'lance', 'laque', 'larva',
        'lasca', 'laser', 'latao', 'latex', 'latim', 'latir', 'lauda', 'lebre', 'legar', 'lemur',
        'lenco', 'lenha', 'lenho', 'lente', 'leoas', 'leque', 'lerdo', 'lesar', 'lesma', 'leste',
        'letao', 'lhama', 'lhano', 'limao', 'limar', 'limpa', 'lince', 'linfa', 'linho', 'lirio',
        'lista', 'litio', 'litro', 'livra', 'lixao', 'lixar', 'lobby', 'locao', 'locar', 'login',
        'logon', 'loira', 'loiro', 'lomba', 'lombo', 'longa', 'lorde', 'lotar', 'lotus', 'loura',
        'lumen', 'lunar', 'lupas', 'lupus', 'luteo', 'luvas', 'luxar', 'luzir', 'macom', 'madre',
        'mafia', 'magma', 'magno', 'magra', 'magro', 'major', 'malar', 'malha', 'malho', 'malte',
        'malva', 'mamao', 'mamar', 'manco', 'manda', 'mando', 'manta', 'marco', 'maria', 'marra',
        'marta', 'marte', 'match', 'meada', 'meado', 'mecha', 'media', 'media', 'medio', 'melao',
        'melar', 'menor', 'menta', 'mento', 'meson', 'metal', 'meter', 'metro', 'metro', 'miada',
        'miado', 'micra', 'micro', 'migar', 'milha', 'milho', 'miolo', 'mioma', 'mirar', 'mirim',
        'mirra', 'missa', 'misso', 'mitra', 'miudo', 'mixar', 'mixer', 'mobil', 'modem', 'moeda',
        'moela', 'mofar', 'mofos', 'mogno', 'moido', 'moita', 'molar', 'molha', 'molhe', 'monja',
        'morbo', 'morna', 'morno', 'morsa', 'morse', 'morto', 'mosca', 'motel', 'motor', 'moura',
        'mouro', 'mouse', 'mudez', 'mufla', 'mumia', 'munir', 'mural', 'murar', 'murro', 'murta',
        'musgo', 'musse', 'mutum', 'nacre', 'nadar', 'nafta', 'naipe', 'nardo', 'nasal', 'nauta',
        'naval', 'negra', 'nervo', 'netos', 'neura', 'neuro', 'nevao', 'nevar', 'nevoa', 'ninfa',
        'ninho', 'ninja', 'nisei', 'nisso', 'nisto', 'nitro', 'niveo', 'nobel', 'nodal', 'noiva',
        'noivo', 'norte', 'notar', 'novel', 'novos', 'nubil', 'nudez', 'nurse', 'nylon', 'obeso',
        'obrar', 'oculo', 'odeon', 'oeste', 'ofega', 'ofego', 'ogiva', 'oitao', 'olear', 'ombro',
        'omega', 'opala', 'opera', 'orcar', 'orgia', 'orixa', 'orlar', 'osseo', 'ossos', 'ostra',
        'otico', 'otite', 'ovada', 'ovado', 'ovino', 'ovulo', 'oxido', 'padre', 'pager', 'palco',
        'palha', 'palio', 'palma', 'palmo', 'palpo', 'pampa', 'panda', 'papai', 'papal', 'papao',
        'papar', 'parda', 'parir', 'parka', 'parra', 'parto', 'passe', 'pasta', 'pasto', 'patao',
        'patua', 'pausa', 'pavao', 'pavio', 'pecar', 'pedal', 'pegao', 'pejar', 'pelar', 'pente',
        'perca', 'perla', 'perna', 'perro', 'persa', 'perua', 'pesca', 'piaba', 'piano', 'picar',
        'picho', 'picto', 'piela', 'pifao', 'pifar', 'pilao', 'pilha', 'pinca', 'pingo', 'pinha',
        'pinta', 'pinto', 'piora', 'pirao', 'pirar', 'pires', 'pisca', 'pisco', 'pista', 'pitao',
        'pitar', 'plato', 'plexo', 'plush', 'pocao', 'podre', 'polca', 'polen', 'polia', 'polio',
        'polir', 'polme', 'polpa', 'polvo', 'pomba', 'pombo', 'ponta', 'ponte', 'porao', 'porco',
        'porre', 'porto', 'posar', 'poser', 'posta', 'poste', 'potra', 'potro', 'pouca', 'poupa',
        'pousa', 'pouso', 'praca', 'prata', 'prato', 'prega', 'prego', 'prelo', 'preta', 'prima',
        'primo', 'prior', 'priva', 'prono', 'provo', 'pubis', 'pudim', 'pugil', 'pulga', 'pulso',
        'punga', 'punho', 'punir', 'purga', 'puxao', 'quark', 'quati', 'quedo', 'queen', 'quepe',
        'quibe', 'quilo', 'quina', 'quita', 'quite', 'quito', 'quivi', 'racao', 'racha', 'radar',
        'ragla', 'raiar', 'raide', 'raiom', 'raios', 'rajar', 'ralar', 'ralho', 'rally', 'ramal',
        'ramos', 'rampa', 'rango', 'ranho', 'rapar', 'rapel', 'rapto', 'raque', 'rasar', 'rasgo',
        'raspa', 'rasta', 'rasto', 'ratao', 'razia', 'reata', 'recta', 'recto', 'recua', 'recuo',
        'redea', 'redox', 'refez', 'refil', 'regar', 'regia', 'regua', 'reich', 'reiki', 'relax',
        'relha', 'remar', 'remix', 'renal', 'repto', 'resma', 'retal', 'retor', 'retos', 'retro',
        'reuma', 'revir', 'ricar', 'rifar', 'rifle', 'rimar', 'rimas', 'rimel', 'rinha', 'ripar',
        'risca', 'rixar', 'rocio', 'rodar', 'rodio', 'roido', 'rojao', 'rolar', 'rolha', 'rombo',
        'romeu', 'ronca', 'ronco', 'ronda', 'ronha', 'roque', 'rosar', 'rosca', 'roseo', 'rosto',
        'rotar', 'rotor', 'roubo', 'rouco', 'rouge', 'rublo', 'rubor', 'rudez', 'ruela', 'rufar',
        'rugar', 'rugbi', 'rugby', 'rugir', 'ruiva', 'ruivo', 'rumar', 'rumba', 'rumen', 'rupia',
        'rusga', 'russo', 'sabao', 'sabre', 'cache', 'sacro', 'safar', 'safra', 'saido', 'salao',
        'salga', 'salsa', 'salto', 'sanha', 'santa', 'saque', 'saque', 'saque', 'sarar', 'sarda',
        'sardo', 'sarja', 'sarna', 'sarro', 'sauna', 'sauna', 'sauva', 'secar', 'sedan', 'sedar',
        'selim', 'selva', 'semen', 'senha', 'sepia', 'septo', 'serra', 'serva', 'sheik', 'short',
        'shoyu', 'sidra', 'sifao', 'sigma', 'signa', 'silex', 'silfa', 'silfo', 'silva', 'simil',
        'siria', 'sirio', 'sisal', 'sismo', 'skate', 'slack', 'slide', 'soada', 'sobra', 'socar',
        'sodio', 'sogra', 'sogro', 'solar', 'solda', 'somar', 'sonar', 'sonda', 'sopor', 'sopro',
        'sorgo', 'sorva', 'sousa', 'sovar', 'spray', 'staff', 'still', 'strip', 'suado', 'sucre',
        'sueca', 'sueco', 'suede', 'sufle', 'suica', 'suico', 'suino', 'suite', 'sujar', 'sulfa',
        'super', 'surda', 'surdo', 'surfe', 'surra', 'sushi', 'susto', 'sutia', 'sutra', 'swell',
        'tabla', 'tabua', 'tabua', 'tacao', 'tacar', 'tacho', 'tacto', 'taifa', 'taipa', 'talao',
        'talar', 'talco', 'talha', 'talhe', 'talho', 'tampa', 'tampo', 'tanga', 'tango', 'tapar',
        'tapir', 'tapiz', 'tarar', 'tardo', 'tarja', 'tarso', 'tatos', 'tatui', 'taxar', 'taxis',
        'taxon', 'tecla', 'tecno', 'teima', 'teina', 'teipe', 'telao', 'telar', 'telex', 'telha',
        'telho', 'temao', 'tenda', 'tenia', 'tenor', 'tensa', 'tense', 'tenta', 'tente', 'tergo',
        'tesar', 'tesla', 'testa', 'testo', 'tetas', 'tiara', 'tibia', 'tibio', 'ticar', 'tiete',
        'tifao', 'tigre', 'tilia', 'timao', 'tinir', 'tinta', 'tinto', 'tipoi', 'tique', 'tirao',
        'titia', 'titio', 'tocha', 'togas', 'tolda', 'toldo', 'tolho', 'tomba', 'tombo', 'tonal',
        'tonar', 'tonel', 'toner', 'tonga', 'tonta', 'tonto', 'tonus', 'topar', 'topaz', 'torax',
        'torce', 'tordo', 'torno', 'torra', 'torre', 'torta', 'torto', 'tosse', 'total', 'totem',
        'touca', 'touro', 'traca', 'trair', 'trans', 'trapa', 'trapo', 'trava', 'trave', 'travo',
        'treco', 'trela', 'trema', 'trena', 'treno', 'treno', 'trepa', 'treva', 'trevo', 'treze',
        'triar', 'trico', 'trigo', 'trino', 'tripa', 'tripe', 'trole', 'trono', 'tropa', 'trote',
        'trova', 'trufa', 'truta', 'tufao', 'tufar', 'tulha', 'tumor', 'tunel', 'tunga', 'turbo',
        'turca', 'turco', 'turfa', 'turfe', 'turne', 'turno', 'turra', 'tweed', 'twist', 'uivar',
        'ultra', 'umero', 'uncao', 'ungir', 'unido', 'untar', 'urano', 'ureia', 'ureia', 'urico',
        'urina', 'urrar', 'urubu', 'urutu', 'usado', 'usina', 'usque', 'usual', 'utero', 'uvula',
        'vagao', 'vagem', 'vaiar', 'valeu', 'valsa', 'vapor', 'varal', 'varar', 'varia', 'variz',
        'vasco', 'vazar', 'veado', 'venda', 'venta', 'venus', 'veras', 'veraz', 'verga', 'verme',
        'vespa', 'veste', 'vexar', 'vidao', 'vidro', 'viela', 'vigil', 'vilar', 'vinca', 'vinco',
        'vinda', 'vindo', 'vinho', 'vinil', 'vinte', 'viola', 'viral', 'virgo', 'visco', 'visgo',
        'visom', 'visor', 'viuva', 'viuvo', 'voada', 'vocal', 'vodca', 'vogal', 'vogar', 'volei',
        'votar', 'xerox', 'xerox', 'xogum', 'zanga', 'zebra', 'zerar', 'ziper', 'zonzo', 'zorra',
        'zorro', 'zunir',
    ];

    // -----------------------------------------------------------------------
    // Público
    // -----------------------------------------------------------------------

    /**
     * Inicia um novo jogo, sorteando uma palavra secreta, e persiste o estado.
     */
    public function iniciarJogo(): array
    {
        $palavraSecreta = $this->sortearPalavra();
        $idJogo         = (string) Str::uuid();

        $estado = [
            'idJogo'              => $idJogo,
            'tamanhoPalavra'      => self::TAMANHO_PALAVRA,
            'tentativasMaximas'   => self::TENTATIVAS_MAX,
            'tentativasRestantes' => self::TENTATIVAS_MAX,
            'palavraSecreta'      => $palavraSecreta,
            'venceu'              => false,
        ];

        $this->salvarEstado($idJogo, $estado);

        // Retorna apenas o necessário para o frontend
        return [
            'idJogo'            => $idJogo,
            'tamanhoPalavra'    => self::TAMANHO_PALAVRA,
            'tentativasMaximas' => self::TENTATIVAS_MAX,
        ];
    }

    /**
     * Busca o estado persistido de um jogo pelo ID.
     */
    public function buscarJogo(string $idJogo): ?array
    {
        return $this->carregarEstado($idJogo);
    }

    /**
     * Verifica se a palavra existe no dicionário (já normalizada).
     */
    public function palavraExiste(string $palavra): bool
    {
        return in_array($palavra, $this->palavras, true);
    }

    /**
     * Remove acentos e normaliza a palavra para comparação interna.
     */
    public function normalizarPalavra(string $palavra): string
    {
        $palavra = mb_strtolower(trim($palavra));

        // Remove acentos e cedilha para normalização interna
        $de   = ['á','à','ã','â','ä','é','è','ê','ë','í','ì','î','ï',
                  'ó','ò','õ','ô','ö','ú','ù','û','ü','ç','ñ',
                  'Á','À','Ã','Â','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï',
                  'Ó','Ò','Õ','Ô','Ö','Ú','Ù','Û','Ü','Ç','Ñ'];
        $para = ['a','a','a','a','a','e','e','e','e','i','i','i','i',
                  'o','o','o','o','o','u','u','u','u','c','n',
                  'a','a','a','a','a','e','e','e','e','i','i','i','i',
                  'o','o','o','o','o','u','u','u','u','c','n'];

        return str_replace($de, $para, $palavra);
    }

    /**
     * Valida a tentativa, atualiza o estado e retorna o resultado.
     */
    public function validarTentativa(string $idJogo, string $palavra): array
    {
        $jogo    = $this->carregarEstado($idJogo);
        $secreta = $jogo['palavraSecreta'];

        $resultado = $this->calcularResultado($palavra, $secreta);

        $venceu = ($palavra === $secreta);
        $jogo['tentativasRestantes']--;

        if ($venceu) {
            $jogo['venceu'] = true;
        }

        $this->salvarEstado($idJogo, $jogo);

        return [
            'resultado'           => $resultado,
            'venceu'              => $venceu,
            'tentativasRestantes' => $jogo['tentativasRestantes'],
            'palavraValida'       => true,
        ];
    }

    // -----------------------------------------------------------------------
    // Privado — Lógica do Jogo
    // -----------------------------------------------------------------------

    /**
     * Algoritmo principal: compara letra a letra e retorna os status.
     * Implementa a lógica correta para letras duplicadas (como no Wordle).
     */
    private function calcularResultado(string $tentativa, string $secreta): array
    {
        $tam       = mb_strlen($secreta);
        $resultado = array_fill(0, $tam, null);

        // Converte strings em arrays de caracteres (suporte a UTF-8)
        $letrasSecreta   = mb_str_split($secreta);
        $letrasTentativa = mb_str_split($tentativa);

        // ---- Passo 1: marcar as corretas ----
        $contadorSecreta = array_fill_keys(array_unique($letrasSecreta), 0);
        foreach ($letrasSecreta as $ch) {
            $contadorSecreta[$ch] = ($contadorSecreta[$ch] ?? 0) + 1;
        }

        for ($i = 0; $i < $tam; $i++) {
            if ($letrasTentativa[$i] === $letrasSecreta[$i]) {
                $resultado[$i] = ['letra' => $letrasTentativa[$i], 'status' => 'correta'];
                $contadorSecreta[$letrasTentativa[$i]]--;
            }
        }

        // ---- Passo 2: marcar presentes e ausentes ----
        for ($i = 0; $i < $tam; $i++) {
            if ($resultado[$i] !== null) {
                continue; // já marcado como correta
            }

            $ch = $letrasTentativa[$i];
            if (isset($contadorSecreta[$ch]) && $contadorSecreta[$ch] > 0) {
                $resultado[$i] = ['letra' => $ch, 'status' => 'presente'];
                $contadorSecreta[$ch]--;
            } else {
                $resultado[$i] = ['letra' => $ch, 'status' => 'ausente'];
            }
        }

        return $resultado;
    }

    private function sortearPalavra(): string
    {
        return $this->palavras[array_rand($this->palavras)];
    }

    // -----------------------------------------------------------------------
    // Privado — Persistência (arquivo JSON por partida)
    // -----------------------------------------------------------------------

    private function caminhoArquivo(string $idJogo): string
    {
        $dir = storage_path('app/' . self::STORAGE_PATH);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir . '/' . $idJogo . '.json';
    }

    private function salvarEstado(string $idJogo, array $estado): void
    {
        file_put_contents(
            $this->caminhoArquivo($idJogo),
            json_encode($estado, JSON_UNESCAPED_UNICODE)
        );
    }

    private function carregarEstado(string $idJogo): ?array
    {
        $arquivo = $this->caminhoArquivo($idJogo);
        if (! file_exists($arquivo)) {
            return null;
        }

        $conteudo = file_get_contents($arquivo);

        return json_decode($conteudo, true);
    }
}
