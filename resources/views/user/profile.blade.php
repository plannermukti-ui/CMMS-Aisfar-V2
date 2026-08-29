@extends('layouts.user')

@section('title', 'Profile')

@section('content')

<!--begin::Profile Header-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-column align-items-center">
            <!--begin::Avatar-->
            <div class="symbol symbol-100px symbol-circle mb-5 border border-3 border-success">
                <div class="symbol-label fs-3 bg-light-primary text-primary" style="background-image:url('{{ asset('assets/metronic/media/avatars/300-1.jpg') }}')">
                    @if(!file_exists(public_path('assets/metronic/media/avatars/300-1.jpg')))
                        <span class="fs-1">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</span>
                    @endif
                </div>
            </div>
            <!--end::Avatar-->

            <!--begin::Name-->
            <div class="d-flex align-items-center mb-2">
                <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ auth()->user()->full_name ?? 'Jenny Klabber' }}</a>
                <a href="#">
                    <i class="ki-duotone ki-verify fs-1 text-primary">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </a>
            </div>
            <!--end::Name-->

            <!--begin::Info-->
            <div class="d-flex flex-wrap flex-center fw-semibold fs-6 mb-4 pe-2">
                <div class="d-flex align-items-center text-gray-500 me-5 mb-2">
                    <i class="ki-outline ki-profile-circle fs-4 me-1 text-gray-400"></i> 
                    {{ auth()->user()->position->name ?? 'Staff' }}
                </div>
                <div class="d-flex align-items-center text-gray-500 me-5 mb-2">
                    <i class="ki-outline ki-geolocation fs-4 me-1 text-gray-400"></i> 
                    {{ auth()->user()->address ?? 'Jakarta, Indonesia' }}
                </div>
                <div class="d-flex align-items-center text-gray-500 mb-2">
                    <i class="ki-outline ki-sms fs-4 me-1 text-gray-400"></i> 
                    {{ auth()->user()->email ?? '-' }}
                </div>
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->

        <!--begin::Navs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mt-5 d-flex justify-content-between align-items-center">
            <div class="d-flex">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#">
                        Profiles <i class="ki-duotone ki-down fs-5 ms-1"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">
                        Projects <i class="ki-duotone ki-down fs-5 ms-1"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Works</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Teams</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Network</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Activity</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">
                        More <i class="ki-duotone ki-down fs-5 ms-1"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
            </div>
            <div class="d-flex align-items-center pb-3">
                <a href="#" class="btn btn-sm btn-primary me-2">
                    <i class="ki-duotone ki-user-tick fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Connect
                </a>
                <a href="#" class="btn btn-sm btn-light btn-icon me-2">
                    <i class="ki-duotone ki-message-text-2 fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </a>
                <a href="#" class="btn btn-sm btn-light btn-icon">
                    <i class="ki-duotone ki-dots-vertical fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </a>
            </div>
        </ul>
        <!--begin::Navs-->
    </div>
</div>
<!--end::Profile Header-->

<!--begin::Content row-->
<div class="row g-5 g-xxl-8">
    <!--begin::Col Left-->
    <div class="col-xl-4">
        <!--begin::About-->
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0">
                <h3 class="card-title fw-bold text-dark">About</h3>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex align-items-center mb-5">
                    <div class="fs-6 fw-semibold text-gray-500 w-125px">Departement</div>
                    <div class="fs-6 fw-bold text-gray-800">{{ auth()->user()->department->name ?? '-' }}</div>
                </div>
                <div class="d-flex align-items-center mb-5">
                    <div class="fs-6 fw-semibold text-gray-500 w-125px">Jabatan</div>
                    <div class="fs-6 fw-bold text-gray-800">{{ auth()->user()->position->name ?? '-' }}</div>
                </div>
                <div class="d-flex align-items-center mb-5">
                    <div class="fs-6 fw-semibold text-gray-500 w-125px">NIK</div>
                    <div class="fs-6 fw-bold text-gray-800">{{ auth()->user()->nik ?? '-' }}</div>
                </div>
                <div class="d-flex align-items-center mb-5">
                    <div class="fs-6 fw-semibold text-gray-500 w-125px">Tahun Bergabung</div>
                    <div class="fs-6 fw-bold text-gray-800">{{ auth()->user()->join_year ?? '-' }}</div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="fs-6 fw-semibold text-gray-500 w-125px">Age</div>
                    <div class="fs-6 fw-bold text-gray-800">
                        @if(auth()->user()->date_of_birth)
                            {{ \Carbon\Carbon::parse(auth()->user()->date_of_birth)->age }} Tahun
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end::About-->
    </div>
    <!--end::Col Left-->

    <!--begin::Col Right-->
    <div class="col-xl-8">
        <!--begin::Tabs Widget-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header card-header-stretch pb-0">
                <div class="card-title">
                    <h3 class="m-0 text-gray-900">Activities</h3>
                </div>
                <div class="card-toolbar m-0">
                    <ul class="nav nav-stretch nav-line-tabs border-transparent" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a id="kt_tab_wo_activity_tab" class="nav-link fs-5 fw-bold me-5 active" data-bs-toggle="tab" role="tab" href="#kt_tab_wo_activity">
                                WO Activity
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="kt_tab_mar_activity_tab" class="nav-link fs-5 fw-bold" data-bs-toggle="tab" role="tab" href="#kt_tab_mar_activity">
                                MAR Activity
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!--begin::Tab pane WO Activity-->
                    <div class="tab-pane fade show active" id="kt_tab_wo_activity" role="tabpanel">
                        @if(isset($workOrders) && $workOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted fs-7">
                                            <th>No. WO</th>
                                            <th>Unit & Lokasi</th>
                                            <th>Judul Pekerjaan</th>
                                            <th>Status</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workOrders as $wo)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('plt.workorder') }}" class="text-gray-900 fw-bold text-hover-primary d-block fs-6">
                                                        {{ $wo->wo_number }}
                                                    </a>
                                                    <span class="text-muted fs-8">{{ $wo->wo_date->format('d/m/Y') }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary me-2">
                                                            <i class="ki-outline ki-wrench fs-4 text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-900 fw-bold fs-7">{{ $wo->equipment->unit ?? 'Unit N/A' }}</span>
                                                            <span class="text-muted fs-8 d-block">{{ $wo->site->site_name ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-gray-800 fw-semibold fs-7 text-truncate d-inline-block" style="max-width: 200px;">
                                                        {{ $wo->job_title }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $wo->status_badge['class'] }} fs-8 fw-bold">
                                                        {{ $wo->status_badge['label'] }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-icon btn-light-primary">
                                                        <i class="ki-outline ki-arrow-right fs-4"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <i class="ki-outline ki-document fs-4x text-muted mb-3"></i>
                                <div class="fs-5 fw-semibold text-gray-600 mb-1">Belum Ada Aktivitas Work Order</div>
                                <span class="fs-7 text-muted">Work Order yang ditugaskan kepada Anda akan muncul di sini.</span>
                            </div>
                        @endif
                    </div>
                    <!--end::Tab pane-->

                    <!--begin::Tab pane MAR Activity-->
                    <div class="tab-pane fade" id="kt_tab_mar_activity" role="tabpanel">
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-notepad-edit fs-5x text-muted mb-5"><span class="path1"></span><span class="path2"></span></i>
                            <div class="fs-4 fw-semibold text-gray-500">Belum ada MAR Activity</div>
                        </div>
                    </div>
                    <!--end::Tab pane-->
                </div>
            </div>
        </div>
        <!--end::Tabs Widget-->
    </div>
    <!--end::Col Right-->
</div>
<!--end::Content row-->

@endsection
