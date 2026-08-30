@php
    $generalSettings = rescue(fn () => app(\App\Settings\GeneralSettings::class), report: false);
    $companyLogo = $generalSettings?->site_logo ? asset('storage/'.$generalSettings->site_logo) : null;
    $companyName = $generalSettings?->site_name ?: 'PT MULTI ARTHA MINERAL';
    $companyAddress = $generalSettings?->company_address ?: 'Kantor Pusat: Gedung Menara Hijau Lt. 10, Jl. MT Haryono Kav. 33, Jakarta Selatan | Site: Jobsite Harindo Wahana (HAWA)';
@endphp

<div class="letterhead-wrapper">
    <div class="letterhead-main">
        <div class="letterhead-logo">
            @if($companyLogo)
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                <div class="letterhead-logo-placeholder" style="display: none;">
                    {{ strtoupper(substr($companyName, 0, 3)) }}
                </div>
            @else
                <div class="letterhead-logo-placeholder">
                    {{ strtoupper(substr($companyName, 0, 3)) }}
                </div>
            @endif
        </div>
        <div class="letterhead-text">
            <h1 class="letterhead-company-name">{{ $companyName }}</h1>
            <div class="letterhead-company-sub">MINING CONTRACTOR & HEAVY EQUIPMENT SERVICES</div>
            <div class="letterhead-company-address">{{ $companyAddress }}</div>
        </div>
    </div>
    <div class="letterhead-divider"></div>
</div>
