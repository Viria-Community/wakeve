<?php

use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AgencyActivityController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ApplicationDocumentController;
use App\Http\Controllers\ApplicationStatusController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyStatusController;
use App\Http\Controllers\CustomRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardOffreController;
use App\Http\Controllers\DepartureController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\EmployeeAccountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FleetUnitController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlatformRoleController;
use App\Http\Controllers\PlatformStaffController;
use App\Http\Controllers\PublicApplicationStatusController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home')->name('home');
Route::view('/conditions-generales', 'public.terms')->name('legal.terms');
Route::view('/politique-de-confidentialite', 'public.privacy')->name('legal.privacy');
Route::get('/suivi-de-demande', [PublicApplicationStatusController::class, 'create'])->name('public.application-status');
Route::post('/suivi-de-demande', [PublicApplicationStatusController::class, 'show'])->middleware('throttle:10,1')->name('public.application-status.show');
Route::get('/sitemap.xml', function () {
    $urls = [
        ['location' => route('home'), 'priority' => '1.0'],
        ['location' => route('registration.identification'), 'priority' => '0.8'],
        ['location' => route('public.application-status'), 'priority' => '0.6'],
        ['location' => route('legal.terms'), 'priority' => '0.3'],
        ['location' => route('legal.privacy'), 'priority' => '0.3'],
    ];

    return response()
        ->view('public.sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/inscription', [PublicRegistrationController::class, 'identification'])->name('registration.identification');
    Route::post('/inscription', [PublicRegistrationController::class, 'storeIdentification'])->name('registration.identification.store');
    Route::get('/inscription/gestionnaire', [PublicRegistrationController::class, 'manager'])->name('registration.manager');
    Route::post('/inscription/gestionnaire', [PublicRegistrationController::class, 'storeManager'])->name('registration.manager.store');
    Route::get('/inscription/modes', [PublicRegistrationController::class, 'modes'])->name('registration.modes');
    Route::post('/inscription/modes', [PublicRegistrationController::class, 'storeModes'])->name('registration.modes.store');
    Route::get('/inscription/documents', [PublicRegistrationController::class, 'documents'])->name('registration.documents');
    Route::post('/inscription/documents', [PublicRegistrationController::class, 'submit'])->name('registration.submit');
    Route::get('/inscription/confirmation/{reference}', [PublicRegistrationController::class, 'success'])->name('registration.success');
    Route::get('/inscription/confirmation/{reference}/recapitulatif', [PublicRegistrationController::class, 'summary'])->name('registration.summary');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications/{notification}', NotificationController::class)->name('notifications.read');
    Route::get('/ma-demande', ApplicationStatusController::class)->name('application.status');
    Route::post('/ma-demande/renvoyer', [ApplicationStatusController::class, 'resubmit'])->name('application.resubmit');
    Route::post('/ma-demande/documents', [ApplicationDocumentController::class, 'store'])->name('application.documents.store');
    Route::put('/ma-demande/documents/{companyDocument}', [ApplicationDocumentController::class, 'update'])->name('application.documents.update');
    Route::get('/ma-demande/documents/{companyDocument}/telecharger', [ApplicationDocumentController::class, 'download'])->name('application.documents.download');
    Route::get('/ma-demande/documents/{companyDocument}/consulter', [ApplicationDocumentController::class, 'preview'])->name('application.documents.preview');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::middleware(['company.operational', 'agency.operational'])->group(function () {
        Route::resource('roles', CustomRoleController::class)->except(['show', 'destroy']);
        Route::resource('platform-roles', PlatformRoleController::class)->except(['show', 'destroy']);
        Route::resource('platform-staff', PlatformStaffController::class)->parameters(['platform-staff' => 'platformStaff'])->except(['show', 'destroy']);
        Route::get('/employees/{employee}/account', [EmployeeAccountController::class, 'edit'])->name('employees.account.edit');
        Route::put('/employees/{employee}/account', [EmployeeAccountController::class, 'update'])->name('employees.account.update');
        Route::put('/employees/{employee}/permissions', [CustomRoleController::class, 'assign'])->name('employees.permissions');
        Route::get('/agence/journal', AgencyActivityController::class)->name('agency.activity');
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::get('/administration/journaux', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::resource('companies', CompanyController::class)->except(['destroy']);
        Route::post('/companies/{company}/status', CompanyStatusController::class)->name('companies.status.update');
        Route::resource('agencies', AgencyController::class)->except(['destroy']);
        Route::resource('employees', EmployeeController::class)->except(['destroy']);
        Route::resource('fleet-units', FleetUnitController::class)->except(['destroy']);

        Route::get('/offre/lignes', [LineController::class, 'index'])->name('lines.index');
        Route::get('/offre/lignes/creer', [LineController::class, 'create'])->name('lines.create');
        Route::post('/offre/lignes', [LineController::class, 'store'])->name('lines.store');
        Route::get('/offre/lignes/{line}', [LineController::class, 'show'])->name('lines.show');
        Route::get('/offre/lignes/{line}/modifier', [LineController::class, 'edit'])->name('lines.edit');
        Route::put('/offre/lignes/{line}', [LineController::class, 'update'])->name('lines.update');
        Route::patch('/offre/lignes/{line}/statut', [LineController::class, 'updateStatus'])->name('lines.updateStatus');

        Route::get('/offre/departs', [DepartureController::class, 'index'])->name('departures.index');
        Route::get('/offre/departs/creer', [DepartureController::class, 'create'])->name('departures.create');
        Route::post('/offre/departs', [DepartureController::class, 'store'])->name('departures.store');
        Route::get('/offre/departs/{departure}', [DepartureController::class, 'show'])->name('departures.show');
        Route::get('/offre/departs/{departure}/modifier', [DepartureController::class, 'edit'])->name('departures.edit');
        Route::put('/offre/departs/{departure}', [DepartureController::class, 'update'])->name('departures.update');
        Route::patch('/offre/departs/{departure}/annuler', [DepartureController::class, 'cancel'])->name('departures.cancel');

        Route::get('/offre/lignes/{line}/tarifs', [FareController::class, 'index'])->name('fares.index');
        Route::get('/offre/lignes/{line}/tarifs/creer', [FareController::class, 'create'])->name('fares.create');
        Route::post('/offre/lignes/{line}/tarifs', [FareController::class, 'store'])->name('fares.store');
        Route::get('/offre/lignes/{line}/tarifs/{fare}/modifier', [FareController::class, 'edit'])->name('fares.edit');
        Route::put('/offre/lignes/{line}/tarifs/{fare}', [FareController::class, 'update'])->name('fares.update');
        Route::patch('/offre/lignes/{line}/tarifs/{fare}/statut', [FareController::class, 'updateStatus'])->name('fares.updateStatus');

        Route::get('/offre/recherche', [SearchController::class, 'index'])->name('search.index');

        Route::get('/offre/tableau-de-bord', [DashboardOffreController::class, 'index'])->name('dashboard-offre.index');

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/compagnies/{company}', [DocumentController::class, 'companyDocuments'])->name('documents.companies.show');
        Route::get('/documents/flotte/{fleetUnit}', [DocumentController::class, 'fleetDocuments'])->name('documents.fleet.show');
        Route::get('/companies/{company}/documents/create', [DocumentController::class, 'createForCompany'])->name('company-documents.create');
        Route::post('/companies/{company}/documents', [DocumentController::class, 'storeForCompany'])->name('company-documents.store');
        Route::get('/company-documents/{companyDocument}/edit', [DocumentController::class, 'editCompany'])->name('company-documents.edit');
        Route::put('/company-documents/{companyDocument}', [DocumentController::class, 'updateCompany'])->name('company-documents.update');
        Route::get('/fleet-units/{fleetUnit}/documents/create', [DocumentController::class, 'createForFleetUnit'])->name('fleet-documents.create');
        Route::post('/fleet-units/{fleetUnit}/documents', [DocumentController::class, 'storeForFleetUnit'])->name('fleet-documents.store');
        Route::get('/fleet-documents/{fleetDocument}/edit', [DocumentController::class, 'editFleet'])->name('fleet-documents.edit');
        Route::put('/fleet-documents/{fleetDocument}', [DocumentController::class, 'updateFleet'])->name('fleet-documents.update');
        Route::get('/company-documents/{companyDocument}/download', [DocumentController::class, 'downloadCompany'])->name('company-documents.download');
        Route::get('/fleet-documents/{fleetDocument}/download', [DocumentController::class, 'downloadFleet'])->name('fleet-documents.download');
        Route::get('/company-documents/{companyDocument}/preview', [DocumentController::class, 'previewCompany'])->name('company-documents.preview');
        Route::get('/fleet-documents/{fleetDocument}/preview', [DocumentController::class, 'previewFleet'])->name('fleet-documents.preview');
        Route::put('/company-documents/{companyDocument}/review', [DocumentReviewController::class, 'company'])->name('company-documents.review');
        Route::put('/fleet-documents/{fleetDocument}/review', [DocumentReviewController::class, 'fleet'])->name('fleet-documents.review');
    });
});
