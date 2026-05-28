<?php

use Illuminate\Support\Facades\Route;

// A API não possui rotas web. Todas as rotas estão em routes/api.php
Route::get('/', function () {
    return response()->json(['status' => 'Termoo API online']);
});
