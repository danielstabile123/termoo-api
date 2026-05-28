// Jogo Termoo — front simples

let apiUrl = '';
let idJogo = null;
let linhaAtual = 0;

const inputs = document.querySelectorAll('.inputs input');
const statusEl = document.getElementById('status');
const board = document.getElementById('board');
const toast = document.getElementById('toast');

function mostrarToast(msg, erro) {
    toast.textContent = msg;
    toast.className = erro ? 'show erro' : 'show';
    setTimeout(() => { toast.className = ''; }, 2500);
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

        if (!res.ok) throw new Error();

        const data = await res.json();
        idJogo = data.idJogo;
        statusEl.textContent = 'Conectado';
        montarTabuleiro();
        habilitarJogo(true);
        limparInputs();
        mostrarToast('Jogo iniciado');
    } catch (e) {
        statusEl.textContent = 'Desconectado';
        mostrarToast('Erro ao conectar', true);
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
            const msg = data.erro || (data.detalhes && data.detalhes.join(' ')) || 'Erro';
            mostrarToast(msg, true);
            return;
        }

        if (data.palavraValida === false) {
            mostrarToast('Palavra não existe no dicionário', true);
            return;
        }

        for (let j = 0; j < 5; j++) {
            const celula = document.getElementById('c-' + linhaAtual + '-' + j);
            celula.textContent = data.resultado[j].letra;
            celula.classList.add(data.resultado[j].status);
        }

        linhaAtual++;
        limparInputs();

        if (data.venceu) {
            mostrarToast('Você venceu!');
            habilitarJogo(false);
        } else if (data.tentativasRestantes === 0) {
            mostrarToast('Tentativas acabaram', true);
            habilitarJogo(false);
        }
    } catch (e) {
        mostrarToast('Erro ao enviar', true);
    }
}

// Enter envia palpite
inputs.forEach((input, i) => {
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') enviarPalpite();
        if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus();
    });
    input.addEventListener('input', () => {
        if (input.value && i < inputs.length - 1) inputs[i + 1].focus();
    });
});

if (window.location.origin && !window.location.origin.startsWith('file')) {
    document.getElementById('apiUrl').value = window.location.origin;
}
