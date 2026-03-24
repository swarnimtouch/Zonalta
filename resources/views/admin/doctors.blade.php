@extends('layouts.admin')

@section('title', 'Doctors')
@section('page-title', 'Doctors')

@push('styles')
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="page-title-group">
            <h4>Doctors</h4>
            <p>Manage all registered doctors in the system</p>
        </div>
        <a href="{{ route('admin.doctors.export') }}{{ request('search') ? '?search='.request('search') : '' }}"
           class="btn-add btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>

    {{-- ── Success Alert ── --}}
    @if(session('success'))
        <div class="alert-success-bar">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Live Search Bar ── --}}
    <div class="filter-bar">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text"
                   id="liveSearch"
                   value="{{ request('search') }}"
                   class="filter-input"
                   placeholder="Type to search by name or Employee ID..."
                   autocomplete="off">
            <span class="search-spinner" id="searchSpinner"></span>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         DESKTOP TABLE (≥ 768px)
    ════════════════════════════════════════ --}}
    <div class="glass-card desktop-view">
        <div class="table-wrap">
            <table class="doc-table">
                <thead>
                <tr>
                    <th>SR NO.</th>
                    <th class="th-doctor">Doctor Name</th>
                    <th class="th-doctor">Msl Code</th>
                    <th class="th-doctor">Degree</th>
                    <th class="th-doctor">City</th>
                    <th class="th-employee col-sep">Employee Name</th>
                    <th class="th-employee">Employee Code</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($doctors as $index => $doctor)
                    @php
                        $colors   = ['c1','c2','c3','c4','c5'];
                        $c        = $colors[$index % 5];
                        $photoUrl = $doctor->photo ? asset($doctor->photo) : null;
                    @endphp
                    <tr>
                        <td class="serial-cell">{{ $doctors->firstItem() + $index }}</td>



                        <td>
                            <div class="doc-name-cell">
                                <span class="doc-name-text">{{ $doctor->name }}</span>
                            </div>
                        </td>

                        <td class="text-muted-sm">{{ $doctor->msl_code ?? '—' }}</td>
                        <td><span class="badge-mono">{{ $doctor->degree }}</span></td>
                        <td class="text-muted-sm">{{ $doctor->city ?? '—' }}</td>
                        <td class="col-sep" style="font-weight:500;">{{ $doctor->employee->name ?? '—' }}</td>
                        <td><span class="badge-mono emp">{{ $doctor->employee->employee_code ?? '—' }}</span></td>

                        <td class="text-muted-sm" style="font-size:0.75rem; white-space:nowrap;">
                            {{ $doctor->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </td>

                        <td>
                            <div class="action-btns">
                                {{-- ✅ Route POST hai — @method('DELETE') NAHI chahiye --}}
                                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}"
                                      method="POST"
                                      class="delete-form">
                                    @csrf
                                    {{-- data-name sweet alert mein naam dikhane ke liye --}}
                                    <button type="button"
                                            class="act-btn del btn-delete"
                                            data-name="{{ $doctor->name }}"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="fas fa-user-md"></i>
                                <h5>No records found</h5>
                                <p>No doctors have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($doctors->hasPages())
            <div class="pagination-wrap">
                <div class="page-info">
                    Showing {{ $doctors->firstItem() }}–{{ $doctors->lastItem() }} of {{ $doctors->total() }}
                </div>
                <div class="custom-pagination">
                    @if($doctors->onFirstPage())
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $doctors->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($doctors->getUrlRange(1, $doctors->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $doctors->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($doctors->hasMorePages())
                        <a href="{{ $doctors->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════
         MOBILE CARDS (< 768px)
    ════════════════════════════════════════ --}}
    <div class="mobile-view">

        @forelse($doctors as $index => $doctor)
            @php
                $colors   = ['c1','c2','c3','c4','c5'];
                $c        = $colors[$index % 5];
                $photoUrl = $doctor->photo ? asset($doctor->photo) : null;
            @endphp

            <div class="m-card" style="animation-delay:{{ $index * 0.04 }}s;">

                <div class="m-card-header">

                    <div class="m-card-title">
                        <div class="m-card-name">{{ $doctor->name }}</div>
                        <div class="m-card-sub">{{ $doctor->msl_code }}</div>
                    </div>
                    <span class="m-card-serial-badge">#{{ $doctors->firstItem() + $index }}</span>
                </div>

                <div class="m-section-label doctor">
                    <i class="fas fa-user-md"></i> Doctor Details
                </div>
                <div class="m-card-body">
                    <div class="m-fields-grid">
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-city"></i> Doctor City</div>
                            <div class="m-field-value {{ $doctor->city ? '' : 'muted' }}">{{ $doctor->city ?? 'Not set' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fa-solid fa-book-open"></i> Degree</div>
                            <div class="m-field-value {{ $doctor->degree ? '' : 'muted' }}">{{ $doctor->degree ?? 'Not set' }}</div>
                        </div>

                    </div>
                </div>

                <div class="m-section-label employee">
                    <i class="fas fa-id-card"></i> Employee Details
                </div>
                <div class="m-card-body">
                    <div class="m-fields-grid">
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-user"></i> Employee Name</div>
                            <div class="m-field-value">{{ $doctor->employee->name ?? '—' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-id-badge"></i> Employee Code</div>
                            <div class="m-field-value mono-emp">{{ $doctor->employee->employee_code ?? '—' }}</div>
                        </div>

                    </div>
                </div>

                <div class="m-card-footer">
                    <div class="m-card-date">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $doctor->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                    </div>
                    <form action="{{ route('admin.doctors.destroy', $doctor->id) }}"
                          method="POST"
                          class="delete-form">
                        @csrf
                        <button type="button"
                                class="btn-del-mobile btn-delete"
                                data-name="{{ $doctor->name }}">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </div>

            </div>

        @empty
            <div class="glass-card">
                <div class="empty-state">
                    <i class="fas fa-user-md"></i>
                    <h5>No records found</h5>
                    <p>No doctors have been added yet.</p>
                </div>
            </div>
        @endforelse

        @if($doctors->hasPages())
            <div class="pagination-wrap" style="border:none; padding:4px 0 16px;">
                <div class="page-info">{{ $doctors->firstItem() }}–{{ $doctors->lastItem() }} of {{ $doctors->total() }}</div>
                <div class="custom-pagination">
                    @if($doctors->onFirstPage())
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $doctors->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($doctors->getUrlRange(1, $doctors->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $doctors->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($doctors->hasMorePages())
                        <a href="{{ $doctors->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif

    </div>

    {{-- ── Photo Preview Modal ── --}}
    <div class="photo-modal-overlay" id="photoModal" onclick="closePhotoModal(event)">
        <div class="photo-modal-box">
            <button class="photo-modal-close"
                    onclick="document.getElementById('photoModal').classList.remove('open')">
                <i class="fas fa-times"></i>
            </button>
            <div id="modalImgWrap"></div>
            <div class="photo-modal-name"  id="modalName"></div>
            <div class="photo-modal-empid" id="modalEmpId"></div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- ✅ SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ══════════════════════════════════════
        // SWEET ALERT DELETE CONFIRM
        // ══════════════════════════════════════
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;

            e.preventDefault();

            const form     = btn.closest('.delete-form');
            const docName  = btn.getAttribute('data-name') || 'this doctor';

            Swal.fire({
                title: 'Delete Doctor?',
                html: `Are you sure you want to delete <strong>${docName}</strong>?<br><small style="color:#aaa;">This action cannot be undone.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, Delete',
                cancelButtonText:  '<i class="fas fa-times"></i> Cancel',
                confirmButtonColor: '#e74a3b',
                cancelButtonColor:  '#4e73df',
                background: '#1a2035',
                color: '#e8eaf6',
                iconColor: '#f6c23e',
                customClass: {
                    popup:         'swal-custom-popup',
                    title:         'swal-custom-title',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton:  'swal-cancel-btn',
                },
                reverseButtons: true,
                focusCancel: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        background: '#1a2035',
                        color: '#e8eaf6',
                        didOpen: function () {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // ══════════════════════════════════════
        // SUCCESS TOAST (session success)
        // ══════════════════════════════════════
        @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#1a2035',
            color: '#1cc88a',
            iconColor: '#1cc88a',
        });
        @endif

        // ══════════════════════════════════════
        // LIVE SEARCH
        // ══════════════════════════════════════
        (function () {
            const input   = document.getElementById('liveSearch');
            const spinner = document.getElementById('searchSpinner');
            if (!input) return;

            let timer = null;

            input.addEventListener('keyup', function () {
                clearTimeout(timer);
                const query = this.value.trim();
                spinner.style.display = 'block';

                timer = setTimeout(function () {
                    const baseUrl = '{{ route('admin.doctors.index') }}';
                    const url     = query.length > 0
                        ? baseUrl + '?search=' + encodeURIComponent(query)
                        : baseUrl;
                    window.location.href = url;
                }, 400);
            });
        })();



        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('photoModal').classList.remove('open');
            }
        });
    </script>
@endpush
