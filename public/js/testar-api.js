const baseUrl = document.getElementById('baseUrl');
const metodo = document.getElementById('metodo');
const url = document.getElementById('url');
const corpo = document.getElementById('corpo');
const resposta = document.getElementById('resposta');

function getBase() {
    return baseUrl.value.replace(/\/$/, '');
}

function preencherIniciar() {
    const b = getBase();
    metodo.value = 'POST';
    url.value = b + '/api/iniciar-jogo';
    corpo.value = '';
}

function preencherValidar() {
    const b = getBase();
    metodo.value = 'POST';
    url.value = b + '/api/validar-tentativa';
    corpo.value = JSON.stringify({
        idJogo: 'cole-o-id-aqui',
        palavra: 'carro',
    }, null, 2);
}

async function enviar() {
    resposta.textContent = 'Enviando...';

    const opcoes = {
        method: metodo.value,
        headers: { 'Content-Type': 'application/json' },
    };

    if (metodo.value === 'POST' && corpo.value.trim()) {
        opcoes.body = corpo.value;
    }

    try {
        const res = await fetch(url.value, opcoes);
        const texto = await res.text();
        resposta.textContent = 'Status: ' + res.status + '\n\n' + texto;
    } catch (e) {
        resposta.textContent = 'Erro: ' + e.message;
    }
}

if (window.location.origin && !window.location.origin.startsWith('file')) {
    baseUrl.value = window.location.origin;
    preencherIniciar();
}
