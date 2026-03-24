@extends('layouts.admin')

@section('title', 'Banner')
@section('page-title', 'Banner')

@push('styles')
    <style>
        /* ══════════════════════════════════════
           MEDIA POPUP — Banner & Video
        ══════════════════════════════════════ */

        /* Overlay */
        .media-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.82);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeIn 0.22s ease;
        }
        .media-modal-overlay.open {
            display: flex;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Box */
        .media-modal-box {
            position: relative;
            background: #1a2035;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 28px 24px 22px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            animation: slideUp 0.28s cubic-bezier(.22,.68,0,1.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Close */
        .media-modal-close {
            position: absolute;
            top: 12px;
            right: 14px;
            background: rgba(255,255,255,0.07);
            border: none;
            border-radius: 50%;
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            color: #bbb;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.18s, color 0.18s;
        }
        .media-modal-close:hover { background: rgba(231,74,59,0.25); color: #e74a3b; }

        /* Type badge tabs */
        .media-modal-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 2px;
            align-self: flex-start;
        }
        .media-tab-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #8899bb;
            cursor: pointer;
            transition: background 0.18s, color 0.18s, border-color 0.18s;
        }
        .media-tab-badge.active-banner  { background: rgba(78,115,223,0.18); border-color: rgba(78,115,223,0.45); color: #7ea7f8; }
        .media-tab-badge.active-video   { background: rgba(28,200,138,0.15); border-color: rgba(28,200,138,0.40); color: #1cc88a; }

        /* Media wrap */
        #mediaContent {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(0,0,0,0.3);
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #mediaContent img {
            width: 100%;
            max-height: 340px;
            object-fit: contain;
            display: block;
            border-radius: 12px;
        }
        #mediaContent video {
            width: 100%;
            max-height: 320px;
            border-radius: 12px;
            background: #000;
            display: block;
            outline: none;
        }

        /* Info */
        .media-modal-name {
            font-size: 1rem;
            font-weight: 700;
            color: #e8eaf6;
            letter-spacing: 0.01em;
            text-align: center;
        }
        .media-modal-empid {
            font-size: 0.76rem;
            color: #6b7eaa;
            text-align: center;
            margin-top: -8px;
        }

        /* Download buttons */
        .media-dl-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            margin-top: 2px;
        }
        .media-dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity 0.18s, transform 0.15s;
        }
        .media-dl-btn:hover { opacity: 0.85; transform: translateY(-1px); }
        .media-dl-btn:active { transform: scale(0.97); }
        .btn-dl-banner {
            background: linear-gradient(135deg, #4e73df, #2d5be3);
            color: #fff;
        }
        .btn-dl-video {
            background: linear-gradient(135deg, #1cc88a, #13a870);
            color: #fff;
        }
        .btn-dl-disabled {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.25);
            cursor: not-allowed;
            pointer-events: none;
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* ══════════════════════════════════════
           MOBILE CARDS — extra fields
        ══════════════════════════════════════ */
        .m-media-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 10px 14px 0;
        }
        .m-media-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.76rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: opacity 0.18s, transform 0.15s;
        }
        .m-media-btn:hover { opacity: 0.85; transform: translateY(-1px); }
        .m-btn-banner { background: rgba(78,115,223,0.18); border: 1px solid rgba(78,115,223,0.35); color: #7ea7f8; }
        .m-btn-video  { background: rgba(28,200,138,0.15); border: 1px solid rgba(28,200,138,0.35); color: #1cc88a; }
        .m-btn-disabled { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); color: rgba(255,255,255,0.22); cursor: not-allowed; }

        /* Desktop action column — banner & video buttons */
        .act-btn.banner-btn  { background: rgba(78,115,223,0.15); border: 1px solid rgba(78,115,223,0.3); color: #7ea7f8; }
        .act-btn.video-btn   { background: rgba(28,200,138,0.12); border: 1px solid rgba(28,200,138,0.28); color: #1cc88a; }
        .act-btn.media-disabled { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); color: rgba(255,255,255,0.2); cursor: not-allowed; }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="page-title-group">
            <h4>Banner</h4>
        </div>
        <a href="{{ route('admin.banner.export') }}{{ request('search') ? '?search='.request('search') : '' }}"
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
                    <th>Photo</th>
                    <th class="th-doctor">Doctor Name</th>
                    <th class="th-doctor">Msl Code</th>
                    <th class="th-doctor">Degree</th>
                    <th class="th-doctor">Mobile Number</th>
                    <th class="th-doctor">Address</th>
                    <th class="th-employee col-sep">Employee Name</th>
                    <th class="th-employee">Employee Code</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($banners as $index => $banner)
                    @php
                        $colors     = ['c1','c2','c3','c4','c5'];
                        $c          = $colors[$index % 5];
                        $photoUrl   = $banner->photo  ? asset($banner->photo)  : null;
                        $bannerUrl  = $banner->banner_path ? asset($banner->banner_path) : null;
                        $videoUrl   = $banner->video_path   ? asset($banner->video_path)   : null;
                    @endphp
                    <tr>
                        <td class="serial-cell">{{ $banners->firstItem() + $index }}</td>

                        <td>
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}"
                                     class="photo-thumb"
                                     onclick="openMediaModal('photo', '{{ $photoUrl }}', null, '{{ addslashes($banner->name) }}', '{{ $banner->emp_id }}')"
                                     alt="{{ $banner->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                <span class="text-muted-sm" style="display:none;">—</span>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="doc-name-cell">
                                <span class="doc-name-text">{{ $banner->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge-mono">{{ $banner->msl_code }}</span></td>

                        <td><span class="badge-mono">{{ $banner->degree }}</span></td>
                        <td class="text-muted-sm">{{ $banner->phone ?? '—' }}</td>
                        <td class="text-muted-sm">{{ $banner->address ?? '—' }}</td>
                        <td class="col-sep" style="font-weight:500;">{{ $banner->employee->name ?? '—' }}</td>
                        <td><span class="badge-mono emp">{{ $banner->employee->employee_code ?? '—' }}</span></td>

                        <td class="text-muted-sm" style="font-size:0.75rem; white-space:nowrap;">
                            {{ $banner->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </td>

                        <td>
                            <div class="action-btns">

                                {{-- Banner Image Button --}}
                                @if($bannerUrl)
                                    <button type="button"
                                            class="act-btn banner-btn"
                                            onclick="openMediaModal('banner', null, '{{ $bannerUrl }}', '{{ addslashes($banner->name) }}', '{{ $banner->employee->employee_code ?? '' }}')"
                                            title="View Banner">
                                        <i class="fas fa-image"></i>
                                    </button>
                                @else
                                    <button type="button" class="act-btn media-disabled" title="No Banner" disabled>
                                        <i class="fas fa-image"></i>
                                    </button>
                                @endif

                                {{-- Video Button --}}
                                @if($videoUrl)
                                    <button type="button"
                                            class="act-btn video-btn"
                                            onclick="openMediaModal('video', null, null, '{{ addslashes($banner->name) }}', '{{ $banner->employee->employee_code ?? '' }}', '{{ $videoUrl }}')"
                                            title="View Video">
                                        <i class="fas fa-video"></i>
                                    </button>
                                @else
                                    <button type="button" class="act-btn media-disabled" title="No Video" disabled>
                                        <i class="fas fa-video"></i>
                                    </button>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('admin.banner.destroy', $banner->id) }}"
                                      method="POST"
                                      class="delete-form">
                                    @csrf
                                    <button type="button"
                                            class="act-btn del btn-delete"
                                            data-name="{{ $banner->name }}"
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
                                <p>No banners have been added yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="pagination-wrap">
                <div class="page-info">
                    Showing {{ $banners->firstItem() }}–{{ $banners->lastItem() }} of {{ $banners->total() }}
                </div>
                <div class="custom-pagination">
                    @if($banners->onFirstPage())
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $banners->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($banners->getUrlRange(1, $banners->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $banners->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($banners->hasMorePages())
                        <a href="{{ $banners->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
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

        @forelse($banners as $index => $banner)
            @php
                $colors    = ['c1','c2','c3','c4','c5'];
                $c         = $colors[$index % 5];
                $photoUrl  = $banner->photo         ? asset($banner->photo)         : null;
                $bannerUrl = $banner->banner_path  ? asset($banner->banner_path)  : null;
                $videoUrl  = $banner->video_path   ? asset($banner->video_path)   : null;
            @endphp

            <div class="m-card" style="animation-delay:{{ $index * 0.04 }}s;">

                {{-- Card Header --}}
                <div class="m-card-header">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}"
                             class="m-card-photo"
                             onclick="openMediaModal('photo', '{{ $photoUrl }}', null, '{{ addslashes($banner->name) }}', '{{ $banner->emp_id }}')"
                             alt="{{ $banner->name }}"
                             onerror="this.outerHTML='<div class=\'m-card-av {{ $c }}\'>{{ strtoupper(substr($banner->name,0,1)) }}</div>'">
                    @else
                        <div class="m-card-av {{ $c }}">{{ strtoupper(substr($banner->name, 0, 1)) }}</div>
                    @endif
                    <div class="m-card-title">
                        <div class="m-card-name">{{ $banner->name }}</div>
                        <div class="m-card-sub">{{ $banner->hospital_name }}</div>
                    </div>
                    <span class="m-card-serial-badge">#{{ $banners->firstItem() + $index }}</span>
                </div>

                {{-- Banner & Video Buttons --}}
                <div class="m-media-btns">
                    @if($bannerUrl)
                        <button type="button"
                                class="m-media-btn m-btn-banner"
                                onclick="openMediaModal('banner', null, '{{ $bannerUrl }}', '{{ addslashes($banner->name) }}', '{{ $banner->employee->employee_code ?? '' }}')">
                            <i class="fas fa-image"></i> View Banner
                        </button>
                    @else
                        <button type="button" class="m-media-btn m-btn-disabled" disabled>
                            <i class="fas fa-image"></i> No Banner
                        </button>
                    @endif

                    @if($videoUrl)
                        <button type="button"
                                class="m-media-btn m-btn-video"
                                onclick="openMediaModal('video', null, null, '{{ addslashes($banner->name) }}', '{{ $banner->employee->employee_code ?? '' }}', '{{ $videoUrl }}')">
                            <i class="fas fa-video"></i> View Video
                        </button>
                    @else
                        <button type="button" class="m-media-btn m-btn-disabled" disabled>
                            <i class="fas fa-video"></i> No Video
                        </button>
                    @endif
                </div>

                {{-- Doctor Details --}}
                <div class="m-section-label banner">
                    <i class="fas fa-user-md"></i> Doctor Details
                </div>
                <div class="m-card-body">
                    <div class="m-fields-grid">
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-graduation-cap"></i> Degree</div>
                            <div class="m-field-value {{ $banner->degree ? '' : 'muted' }}">{{ $banner->degree ?? 'Not set' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-phone"></i> Mobile Number</div>
                            <div class="m-field-value {{ $banner->phone ? '' : 'muted' }}">{{ $banner->phone ?? 'Not set' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-map-marker-alt"></i> Address</div>
                            <div class="m-field-value {{ $banner->address ? '' : 'muted' }}">{{ $banner->address ?? 'Not set' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-city"></i> Doctor Msl Code</div>
                            <div class="m-field-value {{ $banner->msl_code ? '' : 'muted' }}">{{ $banner->msl_code ?? 'Not set' }}</div>
                        </div>

                    </div>
                </div>

                {{-- Employee Details --}}
                <div class="m-section-label employee">
                    <i class="fas fa-id-card"></i> Employee Details
                </div>
                <div class="m-card-body">
                    <div class="m-fields-grid">
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-user"></i> Employee Name</div>
                            <div class="m-field-value">{{ $banner->employee->name ?? '—' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-id-badge"></i> Employee Code</div>
                            <div class="m-field-value mono-emp">{{ $banner->employee->employee_code ?? '—' }}</div>
                        </div>
                        <div class="m-field">
                            <div class="m-field-label"><i class="fas fa-map-marker-alt"></i> Employee City</div>
                            <div class="m-field-value {{ $banner->employee->city ? '' : 'muted' }}">{{ $banner->employee->city ?? 'Not set' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="m-card-footer">
                    <div class="m-card-date">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $banner->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                    </div>
                    <form action="{{ route('admin.banner.destroy', $banner->id) }}"
                          method="POST"
                          class="delete-form">
                        @csrf
                        <button type="button"
                                class="btn-del-mobile btn-delete"
                                data-name="{{ $banner->name }}">
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
                    <p>No Banners have been added yet.</p>
                </div>
            </div>
        @endforelse

        @if($banners->hasPages())
            <div class="pagination-wrap" style="border:none; padding:4px 0 16px;">
                <div class="page-info">{{ $banners->firstItem() }}–{{ $banners->lastItem() }} of {{ $banners->total() }}</div>
                <div class="custom-pagination">
                    @if($banners->onFirstPage())
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $banners->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($banners->getUrlRange(1, $banners->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $banners->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($banners->hasMorePages())
                        <a href="{{ $banners->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="page-btn" style="opacity:0.4;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif

    </div>

    {{-- ════════════════════════════════════════
         UNIFIED MEDIA MODAL
         Handles: photo preview / banner image / video
    ════════════════════════════════════════ --}}
    <div class="media-modal-overlay" id="mediaModal" onclick="closeMediaModal(event)">
        <div class="media-modal-box">

            <button class="media-modal-close" onclick="closeMediaModalDirect()">
                <i class="fas fa-times"></i>
            </button>

            {{-- Tabs / type indicator --}}
            <div class="media-modal-tabs" id="mediaTabs"></div>

            {{-- Media content (img or video) --}}
            <div id="mediaContent"></div>

            {{-- Name & employee id --}}
            <div class="media-modal-name"  id="mediaName"></div>
            <div class="media-modal-empid" id="mediaEmpId"></div>

            {{-- Download buttons row --}}
            <div class="media-dl-row" id="mediaDlRow"></div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ══════════════════════════════════════════════════════════
        //  UNIFIED MEDIA MODAL
        //  type: 'photo' | 'banner' | 'video'
        //  photoUrl   – doctor profile photo
        //  bannerUrl  – banner image (uploads/EMP001/banners/...)
        //  name       – doctor name
        //  empCode    – employee code
        //  videoUrl   – video file  (uploads/EMP001/videos/...)
        // ══════════════════════════════════════════════════════════
        function openMediaModal(type, photoUrl, bannerUrl, name, empCode, videoUrl) {
            const modal    = document.getElementById('mediaModal');
            const content  = document.getElementById('mediaContent');
            const tabs     = document.getElementById('mediaTabs');
            const dlRow    = document.getElementById('mediaDlRow');

            document.getElementById('mediaName').textContent  = name     || '';
            document.getElementById('mediaEmpId').textContent = empCode ? 'Employee Code: ' + empCode : '';

            // Stop any playing video when reopening
            content.innerHTML = '';
            tabs.innerHTML    = '';
            dlRow.innerHTML   = '';

            if (type === 'photo') {
                // ── Profile photo (view only) ──
                tabs.innerHTML = `<span class="media-tab-badge active-banner"><i class="fas fa-user"></i> Profile Photo</span>`;

                if (photoUrl && photoUrl !== 'null') {
                    content.innerHTML = `<img src="${photoUrl}" alt="${name}" onerror="this.replaceWith(noMediaPlaceholder('image'))">`;
                } else {
                    content.appendChild(noMediaPlaceholder('image'));
                }

                // No download for profile photo (add if needed)
                dlRow.innerHTML = '';

            } else if (type === 'banner') {
                // ── Banner Image ──
                tabs.innerHTML = `<span class="media-tab-badge active-banner"><i class="fas fa-image"></i> Banner Image</span>`;

                if (bannerUrl && bannerUrl !== 'null') {
                    content.innerHTML = `<img src="${bannerUrl}" alt="${name}" onerror="this.replaceWith(noMediaPlaceholder('image'))">`;

                    // Filename from path
                    const bannerFilename = bannerUrl.split('/').pop() || 'banner.jpg';
                    dlRow.innerHTML = `
                    <a href="${bannerUrl}"
                       download="${bannerFilename}"
                       class="media-dl-btn btn-dl-banner"
                       title="Download Banner Image">
                        <i class="fas fa-download"></i> Download Banner
                    </a>`;
                } else {
                    content.appendChild(noMediaPlaceholder('image'));
                }

            } else if (type === 'video') {
                // ── Video ──
                tabs.innerHTML = `<span class="media-tab-badge active-video"><i class="fas fa-video"></i> Video Banner</span>`;

                if (videoUrl && videoUrl !== 'null') {
                    // Use <video> with controls
                    const vid = document.createElement('video');
                    vid.controls  = true;
                    vid.preload   = 'metadata';
                    vid.style.width  = '100%';
                    vid.style.maxHeight = '320px';
                    vid.style.borderRadius = '12px';
                    vid.style.background = '#000';

                    const src = document.createElement('source');
                    src.src  = videoUrl;
                    // Detect extension for MIME
                    const ext = videoUrl.split('.').pop().toLowerCase();
                    const mime = ext === 'webm' ? 'video/webm' : (ext === 'ogg' ? 'video/ogg' : 'video/mp4');
                    src.type = mime;
                    vid.appendChild(src);

                    vid.onerror = function() {
                        content.innerHTML = '';
                        content.appendChild(noMediaPlaceholder('video'));
                    };

                    content.appendChild(vid);

                    const videoFilename = videoUrl.split('/').pop() || 'video.mp4';
                    dlRow.innerHTML = `
                    <a href="${videoUrl}"
                       download="${videoFilename}"
                       class="media-dl-btn btn-dl-video"
                       title="Download Video">
                        <i class="fas fa-download"></i> Download Video
                    </a>`;
                } else {
                    content.appendChild(noMediaPlaceholder('video'));
                }
            }

            modal.classList.add('open');
        }

        // Placeholder when media fails / missing
        function noMediaPlaceholder(mediaType) {
            const div = document.createElement('div');
            div.style.cssText = `
            width:100%; height:160px; border-radius:12px;
            background:rgba(255,255,255,0.03);
            border:2px dashed rgba(255,255,255,0.08);
            display:flex; flex-direction:column; align-items:center;
            justify-content:center; color:rgba(255,255,255,0.3); gap:10px;`;
            const icon = mediaType === 'video' ? 'fa-video' : 'fa-image';
            const text = mediaType === 'video' ? 'No video available' : 'No image available';
            div.innerHTML = `<i class="fas ${icon}" style="font-size:2.2rem;opacity:0.22;"></i>
                         <span style="font-size:0.82rem;">${text}</span>`;
            return div;
        }

        // Close on overlay click
        function closeMediaModal(e) {
            if (e.target.id === 'mediaModal') closeMediaModalDirect();
        }

        function closeMediaModalDirect() {
            const modal = document.getElementById('mediaModal');
            modal.classList.remove('open');
            // Stop video playback
            const vid = modal.querySelector('video');
            if (vid) { vid.pause(); vid.currentTime = 0; }
        }

        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMediaModalDirect();
        });

        // ══════════════════════════════════════
        // SWEET ALERT DELETE CONFIRM
        // ══════════════════════════════════════
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            e.preventDefault();

            const form    = btn.closest('.delete-form');
            const docName = btn.getAttribute('data-name') || 'this Banner';

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
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        background: '#1a2035',
                        color: '#e8eaf6',
                        didOpen: function () { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });

        // ══════════════════════════════════════
        // SUCCESS TOAST
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
                    const baseUrl = '{{ route('admin.banner.index') }}';
                    const url = query.length > 0
                        ? baseUrl + '?search=' + encodeURIComponent(query)
                        : baseUrl;
                    window.location.href = url;
                }, 400);
            });
        })();
    </script>
@endpush
