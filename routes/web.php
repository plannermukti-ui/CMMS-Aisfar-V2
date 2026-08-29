<?php

use App\Http\Controllers\User\ProfileController;
use App\Livewire\Plt\CcrPage;
use App\Livewire\Plt\ComponentTrackerPage;
use App\Livewire\Plt\DashboardPage as PltDashboardPage;
use App\Livewire\Plt\FarPage;
use App\Livewire\Plt\OsrPage;
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
    return redirect('/plt/dashboard');
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
    Route::get('/components', ComponentTrackerPage::class)->name('plt.components');
    Route::get('/ccr', CcrPage::class)->name('plt.ccr');
    Route::get('/far', FarPage::class)->name('plt.far');
    Route::get('/osr', OsrPage::class)->name('plt.osr');
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
    Route::get('/stock-opname', ScmStockOpnamePage::class)->name('scm.opname');
    Route::get('/mol', ScmMolPage::class)->name('scm.mol');
    Route::get('/pr', ScmPrPage::class)->name('scm.pr');
    Route::get('/rfq', ScmRfqPage::class)->name('scm.rfq');
    Route::get('/po', ScmPoPage::class)->name('scm.po');
    Route::get('/do', ScmDoPage::class)->name('scm.do');
    Route::get('/gr', ScmGrPage::class)->name('scm.gr');
});

// Legacy /user Redirection to /plt
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/{any?}', function ($any = 'dashboard') {
        return redirect('/plt/'.$any);
    })->where('any', '.*');
});
