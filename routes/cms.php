<?php

use App\Http\Controllers\Admin\CMSController;

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('layout', [CMSController::class, 'layout']);
    Route::post('/cms/store', [CmsController::class, 'store'])->name('cms.store');
    
    Route::get('/cms/package', [CmsController::class, 'package'])->name('cms.package');
    Route::get('/cms/create-package', [CmsController::class, 'createPackage']);
    Route::post('/cms/package', [CmsController::class, 'storePackage'])->name('package.store');
    Route::get('/cms/edit-package/{id}', [CmsController::class, 'editPackage']);
    Route::put('/cms/update-package/{id}', [CmsController::class, 'updatePackage'])->name('package.update');
    Route::delete('/cms/delete-package/{id}', [CmsController::class, 'destroyPackage']);
    
    Route::get('/cms/package_poin', [CmsController::class, 'package_poin'])->name('cms.package_poin');
    Route::get('/cms/topup_poin', [CmsController::class, 'topup_poin'])->name('cms.topup_poin');
    Route::post('/cms/store_topup', [CmsController::class, 'storetopup_poin'])->name('package.store_topup');
    Route::put('/cms/update-top_up/{id}', [CmsController::class, 'updateTopUp'])->name('package.topup_update');
    Route::delete('/cms/delete-top_up/{id}', [CmsController::class, 'destroyTopUp'])->name('package.topup.delete');
});
