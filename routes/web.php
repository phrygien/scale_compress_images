<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/{options}/{path}", [ImageController::class, "show"])
    ->where("options", "([a-zA-Z]+=[a-zA-z0-9]+,?)+")
    ->where("path", ".*\..*");
