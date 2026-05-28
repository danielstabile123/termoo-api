# Termoo API — Laravel REST

API REST para o jogo [Termoo](https://term.ooo/), compatível com o frontend em **https://termorest.conradosal.com**.

---

## Endpoints

### `POST /api/iniciar-jogo`
Inicia uma nova partida.

**Resposta 200:**
```json
{
  "idJogo": "550e8400-e29b-41d4-a716-446655440000",
  "tamanhoPalavra": 5,
  "tentativasMaximas": 6
}
```

---

### `POST /api/validar-tentativa`
Valida uma tentativa do jogador.

**Body:**
```json
{
  "idJogo": "550e8400-e29b-41d4-a716-446655440000",
  "palavra": "carro"
}
```

**Resposta 200 (palavra válida):**
```json
{
  "resultado": [
    { "letra": "c", "status": "correta" },
    { "letra": "a", "status": "presente" },
    { "letra": "r", "status": "ausente" },
    { "letra": "r", "status": "correta" },
    { "letra": "o", "status": "correta" }
  ],
  "venceu": false,
  "tentativasRestantes": 5,
  "palavraValida": true
}
```

**Resposta 200 (palavra fora do dicionário):**
```json
{
  "resultado": [],
  "venceu": false,
  "tentativasRestantes": 5,
  "palavraValida": false
}
```

**Códigos de erro:**
- `400` — Requisição inválida (campos faltando, palavra com tamanho errado, jogo já encerrado)
- `404` — Jogo não encontrado

---

## Deploy no Railway

1. Faça push deste repositório para o GitHub (já conectado ao Railway).
2. No Railway, abra o serviço **termoo-api** → **Variables** e adicione (recomendado):
   - `APP_URL` = `https://termoo-api-production-d5e7.up.railway.app` (sua URL real)
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `LOG_CHANNEL` = `stderr` (para ver erros nos logs do Railway)
   - `APP_KEY` = opcional — o script `scripts/start.sh` gera automaticamente se estiver vazio
3. Em **Settings** → confirme que o **Root Directory** está vazio (raiz do repo).
4. Clique em **Redeploy** (ou faça um novo commit).
5. Teste:
   - `GET https://SUA-URL.up.railway.app/` → status da API
   - `POST https://SUA-URL.up.railway.app/api/iniciar-jogo` → inicia partida

> **Importante:** o front do Termoo usa as rotas `/api/...`. A URL base é só `https://SUA-URL.up.railway.app` (sem `/api` no final).

Se o build falhar de novo, abra a aba **Build Logs** e verifique se `composer install` terminou sem erro.

---

## Instalação e Deploy

### Pré-requisitos
- PHP 8.1+
- Composer
- Extensão `intl` e `mbstring` habilitadas

### 1. Clonar e instalar dependências

```bash
git clone <seu-repositório>
cd termoo-api
composer install --optimize-autoloader --no-dev
```

### 2. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite `.env` conforme seu ambiente:
```env
APP_ENV=production
APP_URL=https://sua-url.com
```

### 3. Permissões de storage

```bash
chmod -R 775 storage bootstrap/cache
```

### 4. Configurar o servidor web

#### Apache — `.htaccess` (já incluso no Laravel)
Aponte o DocumentRoot para `public/`.

#### Nginx
```nginx
server {
    listen 80;
    server_name sua-api.com;
    root /var/www/termoo-api/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. (Hospedagem compartilhada) Subir via FTP/cPanel

1. Faça upload de **todos os arquivos** para a pasta raiz do domínio (ex: `public_html/termoo-api/`).
2. Aponte o domínio para `public_html/termoo-api/public/`.
3. No cPanel → Terminal (ou SSH):
   ```bash
   composer install --optimize-autoloader --no-dev
   cp .env.example .env
   php artisan key:generate
   ```

### 6. Testar

```bash
# Iniciar jogo
curl -X POST https://sua-api.com/api/iniciar-jogo \
  -H "Content-Type: application/json"

# Validar tentativa
curl -X POST https://sua-api.com/api/validar-tentativa \
  -H "Content-Type: application/json" \
  -d '{"idJogo":"ID_AQUI","palavra":"carro"}'
```

---

## Arquitetura

```
app/
├── Http/
│   └── Controllers/
│       └── TermooController.php   ← Entrada HTTP, validação de request
├── Services/
│   └── TermooService.php          ← Lógica do jogo + persistência
config/
└── cors.php                        ← CORS liberado para termorest.conradosal.com
routes/
└── api.php                         ← POST /iniciar-jogo, POST /validar-tentativa
storage/app/games/                  ← Estados das partidas (JSON por arquivo)
```

### Como funciona a lógica de cores

O algoritmo implementa corretamente a mesma lógica do Wordle/Termoo para letras duplicadas:

1. **Primeira passagem:** marca posições **corretas** (letra certa no lugar certo) e decrementa o contador daquela letra na palavra secreta.
2. **Segunda passagem:** para posições não marcadas, verifica se a letra existe ainda no contador. Se sim → **presente**; se não → **ausente**.

Isso evita o bug clássico de marcar uma letra como "presente" quando ela já foi completamente "consumida" por ocorrências corretas.

---

## CORS

O arquivo `config/cors.php` libera exclusivamente `https://termorest.conradosal.com`. Para testar localmente, adicione sua origem na lista `allowed_origins`.
