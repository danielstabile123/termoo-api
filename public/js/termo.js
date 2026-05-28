// Front simples do Termoo — consome a API Laravel

let apiUrl = '';
let idJogo = null;
let linhaAtual = 0;

const inputs = document.querySelectorAll('.inputs input');
const statusEl = document.getElementById('status');
const board = document.getElementById('board');
const toast = document.getElementById('toast');

function mostrarToast(msg, erro) {
    toast.textContent = msg;
    toast.className = 'show' + (erro ? ' erro' : '');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

function montarTabuleiro() {
    board.innerHTML = '';
    linhaAtual = 0;
    for (let i = 0; i < 6; i++) {
        const linha = document.createElement('div');
        linha.className = 'linha';
        for (let j = 0; j < 5; j++) {
            const celula = document.createElement('div');
            celula.className = 'celula';
            celula.id = 'c-' + i + '-' + j;
            linha.appendChild(celula);
        }
        board.appendChild(linha);
    }
}

function montarTeclado() {
    const linhas = ['qwertyuiop', 'asdfghjkl', 'zxcvbnm'];
    const teclado = document.getElementById('teclado');
    teclado.innerHTML = '';

    linhas.forEach((letras) => {
        const div = document.createElement('div');
        div.className = 'tecla-linha';
        letras.split('').forEach((letra) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tecla';
            btn.textContent = letra;
            btn.onclick = () => digitarLetra(letra);
            div.appendChild(btn);
        });
        teclado.appendChild(div);
    });
}

function digitarLetra(letra) {
    const vazio = Array.from(inputs).find((i) => !i.value);
    if (vazio) {
        vazio.value = letra;
        vazio.focus();
    }
}

function pegarPalavra() {
    return Array.from(inputs).map((i) => i.value).join('').toLowerCase();
}

function limparInputs() {
    inputs.forEach((i) => (i.value = ''));
    inputs[0].focus();
}

function habilitarJogo(ativo) {
    inputs.forEach((i) => (i.disabled = !ativo));
    document.getElementById('btnEnviar').disabled = !ativo;
}

async function conectar() {
    apiUrl = document.getElementById('apiUrl').value.replace(/\/$/, '');
    if (!apiUrl) {
        mostrarToast('Informe a URL da API', true);
        return;
    }

    try {
        const res = await fetch(apiUrl + '/api/iniciar-jogo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const data = await res.json();
        idJogo = data.idJogo;
        statusEl.textContent = 'Conectado';
        montarTabuleiro();
        habilitarJogo(true);
        limparInputs();
        mostrarToast('Jogo iniciado!');
    } catch (e) {
        statusEl.textContent = 'Desconectado';
        mostrarToast('Erro ao conectar na API', true);
    }
}

async function enviarPalpite() {
    const palavra = pegarPalavra();
    if (palavra.length !== 5) {
        mostrarToast('Digite 5 letras', true);
        return;
    }

    try {
        const res = await fetch(apiUrl + '/api/validar-tentativa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idJogo, palavra }),
        });

        const data = await res.json();

        if (!res.ok) {
            mostrarToast(data.erro || 'Erro na tentativa', true);
            return;
        }

        if (data.palavraValida === false) {
            mostrarToast('Palavra não está no dicionário', true);
            return;
        }

        for (let j = 0; j < 5; j++) {
            const celula = document.getElementById('c-' + linhaAtual + '-' + j);
            const item = data.resultado[j];
            celula.textContent = item.letra;
            celula.classList.add(item.status);
        }

        linhaAtual++;
        limparInputs();

        if (data.venceu) {
            mostrarToast('Você venceu!');
            habilitarJogo(false);
        } else if (data.tentativasRestantes === 0) {
            mostrarToast('Suas tentativas acabaram', true);
            habilitarJogo(false);
        }
    } catch (e) {
        mostrarToast('Erro ao enviar palpite', true);
    }
}

// Se abrir no mesmo servidor da API, preenche a URL automaticamente
if (window.location.origin && !window.location.origin.startsWith('file')) {
    document.getElementById('apiUrl').value = window.location.origin;
}

montarTeclado();
