<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParametreController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Module Inscription
Route::prefix('inscriptions')->middleware('auth')->group(function () {
    Route::get('/', [InscriptionController::class, 'dashboard'])->name('inscriptions.dashboard');
    
    // Paramètres
    Route::get('/parametres', [ParametreController::class, 'inscription'])->name('inscriptions.parametres');
    Route::post('/parametres', [ParametreController::class, 'saveInscription']);
    
    // Niveaux et classes
    Route::get('/niveaux', [InscriptionController::class, 'niveaux'])->name('inscriptions.niveaux');
    Route::post('/niveaux', [InscriptionController::class, 'saveNiveaux']);
    
    // Importation
    Route::get('/import', [InscriptionController::class, 'showImport'])->name('inscriptions.import');
    Route::post('/import', [InscriptionController::class, 'processImport']);
    Route::post('/import/save', [InscriptionController::class, 'saveImportedData'])->name('inscriptions.saveImportedData');
    
    // Élèves
    Route::get('/eleves', [InscriptionController::class, 'eleves'])->name('inscriptions.eleves');
    Route::get('/eleves/{id}', [InscriptionController::class, 'showEleve'])->name('inscriptions.showEleve');
    Route::delete('/eleves/{id}', [InscriptionController::class, 'deleteEleve'])->name('inscriptions.deleteEleve');
    
    // Inscription
    Route::get('/nouvelle', [InscriptionController::class, 'nouvelleInscription'])->name('inscriptions.nouvelle');
    Route::post('/nouvelle', [InscriptionController::class, 'enregistrerInscription']);
    Route::get('/recu/{id}', [InscriptionController::class, 'recu'])->name('inscriptions.recu');
    
    // Rapports
    Route::get('/rapports', [InscriptionController::class, 'rapports'])->name('inscriptions.rapports');
    Route::post('/rapports/generer', [InscriptionController::class, 'genererRapport'])->name('inscriptions.genererRapport');
});

// Module Finance
Route::prefix('finances')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [FinanceController::class, 'dashboard'])->name('finances.dashboard');
    
    // Paramètres financiers
    Route::get('/parametres', [FinanceController::class, 'parametres'])->name('finances.parametres');
    Route::post('/parametres', [FinanceController::class, 'saveParametres'])->name('finances.saveParametres');
    
    // Transactions
    Route::get('/transactions', [FinanceController::class, 'transactions'])->name('finances.transactions');
    Route::post('/transactions', [FinanceController::class, 'ajouterTransaction'])->name('finances.ajouterTransaction');
    Route::delete('/transactions/{id}', [FinanceController::class, 'supprimerTransaction'])->name('finances.supprimerTransaction');
    Route::get('/transactions/{id}/recu', [FinanceController::class, 'recu'])->name('finances.recu');
    
    // Catégories
    Route::post('/categories', [FinanceController::class, 'ajouterCategorie'])->name('finances.ajouterCategorie');
    Route::delete('/categories/{id}', [FinanceController::class, 'supprimerCategorie'])->name('finances.supprimerCategorie');
    
    // Rapports
    Route::get('/rapports', [FinanceController::class, 'rapports'])->name('finances.rapports');
    Route::post('/rapports/generer', [FinanceController::class, 'genererRapport'])->name('finances.genererRapport');
});

// Module Matière
Route::prefix('matieres')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [MatiereController::class, 'dashboard'])->name('matieres.dashboard');
    
    // Paramètres
    Route::get('/parametres', [MatiereController::class, 'parametres'])->name('matieres.parametres');
    Route::post('/parametres', [MatiereController::class, 'saveParametres'])->name('matieres.saveParametres');
    
    // Articles
    Route::get('/articles', [MatiereController::class, 'articles'])->name('matieres.articles');
    Route::get('/articles/nouveau', [MatiereController::class, 'nouvelArticle'])->name('matieres.nouvelArticle');
    Route::post('/articles/nouveau', [MatiereController::class, 'enregistrerArticle']);
    Route::get('/articles/{id}', [MatiereController::class, 'showArticle'])->name('matieres.showArticle');
    Route::get('/articles/{id}/edit', [MatiereController::class, 'editArticle'])->name('matieres.editArticle');
    Route::put('/articles/{id}', [MatiereController::class, 'updateArticle'])->name('matieres.updateArticle');
    
    // Mouvements
    Route::get('/mouvements', [MatiereController::class, 'mouvements'])->name('matieres.mouvements');
    Route::get('/mouvements/nouveau', [MatiereController::class, 'nouveauMouvement'])->name('matieres.nouveauMouvement');
    Route::post('/mouvements/nouveau', [MatiereController::class, 'enregistrerMouvement']);
    
    // Fournisseurs
    Route::get('/fournisseurs', [MatiereController::class, 'fournisseurs'])->name('matieres.fournisseurs');
    Route::get('/fournisseurs/nouveau', [MatiereController::class, 'nouveauFournisseur'])->name('matieres.nouveauFournisseur');
    Route::post('/fournisseurs/nouveau', [MatiereController::class, 'enregistrerFournisseur']);
    Route::get('/fournisseurs/{id}/edit', [MatiereController::class, 'editFournisseur'])->name('matieres.editFournisseur');
    Route::put('/fournisseurs/{id}', [MatiereController::class, 'updateFournisseur'])->name('matieres.updateFournisseur');
    
    // Catégories
    Route::get('/categories', [MatiereController::class, 'categories'])->name('matieres.categories');
    Route::get('/categories/nouvelle', [MatiereController::class, 'nouvelleCategorie'])->name('matieres.nouvelleCategorie');
    Route::post('/categories/nouvelle', [MatiereController::class, 'enregistrerCategorie']);
    
    // Rapports
    Route::get('/rapports', [MatiereController::class, 'rapports'])->name('matieres.rapports');
    Route::post('/rapports/generer', [MatiereController::class, 'genererRapport'])->name('matieres.genererRapport');
});