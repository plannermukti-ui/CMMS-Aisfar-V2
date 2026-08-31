<?php

use App\Http\Controllers\Plt\PltPrintController;
use App\Http\Controllers\Scm\ScmPrintController;
use App\Http\Controllers\User\ProfileController;
use App\Livewire\Plt\CcrPage;
use App\Livewire\Plt\ComponentTrackerPage;
use App\Livewire\Plt\DashboardPage as PltDashboardPage;
use App\Livewire\Plt\FarPage;
use App\Livewire\Plt\HmUpdatePage;
use App\Livewire\Plt\OsrPage;
use App\Livewire\Plt\PmHistoryPage;
use App\Livewire\Plt\PmMonitoringPage;
use App\Livewire\Plt\PmServiceTypePage;
use App\Livewire\Scm\DashboardPage as ScmDashboardPage;
use App\Livewire\Scm\DoPage as ScmDoPage;
use App\Livewire\Scm\GrPage as ScmGrPage;
use App\Livewire\Scm\MolPage as ScmMolPage;
use App\Livewire\Scm\PartsPage as ScmPartsPage;
use App\Livewire\Scm\PoPage as ScmPoPage;
use App\Livewire\Scm\PrPage as ScmPrPage;
use App\Livewire\Scm\RfqPage as ScmRfqPage;
use App\Livewire\Scm\StockOpnamePage as ScmStockOpnamePage;
use App\Livewire\User\ChatPage;
use App\Livewire\User\WorkOrderPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $allowedModules = $user->allowed_modules ?? [];

        // Priority: admin → plt → scm
        if (in_array('admin', $allowedModules)) {
            return redirect('/admin');
        }
        if (in_array('plt', $allowedModules)) {
            return redirect('/plt/dashboard');
        }
        if (in_array('scm', $allowedModules)) {
            return redirect('/scm/dashboard');
        }

        return redirect('/profile');
    }

    return redirect('/admin/login');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

// Global Routes (/chat & /profile)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', ChatPage::class)->name('chat');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

// PLANT Maintenance Module Routes (/plt)
Route::middleware(['auth', 'module_access:plt'])->prefix('plt')->group(function () {
    Route::get('/', function () {
        return redirect('/plt/dashboard');
    });
    Route::get('/dashboard', PltDashboardPage::class)->name('plt.dashboard');
    Route::get('/profile', function () {
        return redirect('/profile');
    })->name('plt.profile');
    Route::get('/workorder', WorkOrderPage::class)->name('plt.workorder');
    Route::get('/workorder/{id}/print', [PltPrintController::class, 'printWorkOrder'])->name('plt.workorder.print');
    Route::get('/components', ComponentTrackerPage::class)->name('plt.components');
    Route::get('/components/{id}/print', [PltPrintController::class, 'printComponent'])->name('plt.components.print');
    Route::get('/hm-update', HmUpdatePage::class)->name('plt.hm-update');
    Route::get('/hm-update/print', [PltPrintController::class, 'printHmUpdate'])->name('plt.hm-update.print');
    Route::get('/ccr', CcrPage::class)->name('plt.ccr');
    Route::get('/ccr/{id}/print', [PltPrintController::class, 'printCcr'])->name('plt.ccr.print');
    Route::get('/far', FarPage::class)->name('plt.far');
    Route::get('/far/{id}/print', [PltPrintController::class, 'printFar'])->name('plt.far.print');
    Route::get('/osr', OsrPage::class)->name('plt.osr');
    Route::get('/osr/{id}/print', [PltPrintController::class, 'printOsr'])->name('plt.osr.print');
    // Preventive Maintenance Routes
    Route::get('/pm-service-types', PmServiceTypePage::class)->name('plt.pm-service-types');
    Route::get('/pm-monitoring', PmMonitoringPage::class)->name('plt.pm-monitoring');
    Route::get('/pm-history', PmHistoryPage::class)->name('plt.pm-history');
    Route::get('/chat', function () {
        return redirect('/chat');
    })->name('plt.chat');
});

// SCM Logistics Module Routes (/scm)
Route::middleware(['auth', 'module_access:scm'])->prefix('scm')->group(function () {
    Route::get('/', function () {
        return redirect('/scm/dashboard');
    });
    Route::get('/dashboard', ScmDashboardPage::class)->name('scm.dashboard');
    Route::get('/parts', ScmPartsPage::class)->name('scm.parts');
    Route::get('/parts/print', [ScmPrintController::class, 'printParts'])->name('scm.parts.print');
    Route::get('/stock-opname', ScmStockOpnamePage::class)->name('scm.opname');
    Route::get('/stock-opname/{id}/print', [ScmPrintController::class, 'printStockOpname'])->name('scm.opname.print');
    Route::get('/mol', ScmMolPage::class)->name('scm.mol');
    Route::get('/mol/{id}/print', [ScmPrintController::class, 'printMol'])->name('scm.mol.print');
    Route::get('/pr', ScmPrPage::class)->name('scm.pr');
    Route::get('/pr/{id}/print', [ScmPrintController::class, 'printPr'])->name('scm.pr.print');
    Route::get('/rfq', ScmRfqPage::class)->name('scm.rfq');
    Route::get('/rfq/{id}/print', [ScmPrintController::class, 'printRfq'])->name('scm.rfq.print');
    Route::get('/po', ScmPoPage::class)->name('scm.po');
    Route::get('/po/{id}/print', [ScmPrintController::class, 'printPo'])->name('scm.po.print');
    Route::get('/do', ScmDoPage::class)->name('scm.do');
    Route::get('/do/{id}/print', [ScmPrintController::class, 'printDo'])->name('scm.do.print');
    Route::get('/gr', ScmGrPage::class)->name('scm.gr');
    Route::get('/gr/{id}/print', [ScmPrintController::class, 'printGr'])->name('scm.gr.print');
});

// Legacy /user Redirection to /plt
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/{any?}', function ($any = 'dashboard') {
        return redirect('/plt/'.$any);
    })->where('any', '.*');
});
