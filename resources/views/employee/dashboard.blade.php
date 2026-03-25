<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zonalta - Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at center, #dcedfb 0%, #b3d7f6 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #1a1a2e;
        }
        .topbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-bottom: 2px solid #204e8a;
        }
        .topbar-logo {
            max-height: 45px;
            width: auto;
        }
        .user-profile {
            display: flex;
            align-items: center;
        }
        .profile-info {
            position: relative;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #204e8a;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .profile-dropdown {
            position: absolute;
            top: 150%;
            right: 0;
            width: 200px;
            background: #ffffff;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1050;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .profile-dropdown::before {
            content: "";
            position: absolute;
            top: -8px;
            right: 20px;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #ffffff;
        }
        .profile-info:hover .profile-dropdown,
        .profile-info.active .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            top: calc(100% + 10px);
        }
        .profile-chevron {
            transition: transform 0.3s ease;
        }
        .profile-info:hover .profile-chevron,
        .profile-info.active .profile-chevron {
            transform: rotate(180deg);
        }
        .profile-dropdown .dropdown-item {
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s ease, color 0.2s ease;
            padding: 10px 15px;
            border-radius: 8px;
        }
        .profile-dropdown .dropdown-item:hover {
            background-color: #f8fafc;
            color: #dc3545 !important;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }
        .custom-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1.5px solid #204e8a;
            padding: 30px 40px;
            margin-bottom: 30px;
        }

        @media (max-width: 575.98px) {
            .custom-card {
                padding: 20px 15px;
            }
        }
        .custom-card h3 {
            color: #204e8a;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1.5px solid #e2e8f0;
        }
        .form-label {
            color: #204e8a;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .icon-input-wrapper {
            position: relative;
        }
        .icon-input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #204e8a;
            font-size: 14px;
        }
        .icon-input-wrapper textarea ~ i, 
        .icon-input-wrapper i.fa-location-dot {
            top: 20px;
        }
        .icon-input-wrapper .form-control,
        .icon-input-wrapper .form-select {
            padding-left: 45px;
            font-size: 14px;
            border-color: #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
        }
        .icon-input-wrapper .form-control:focus,
        .icon-input-wrapper .form-select:focus {
            border-color: #204e8a;
            box-shadow: 0 0 0 0.2rem rgba(32, 78, 138, 0.25);
            background-color: #ffffff;
        }
        .btn-theme {
            background-color: #3461a3;
            color: #ffffff;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        .btn-theme:hover {
            background-color: #204e8a;
            color: #ffffff;
        }
        .photo-section {
            margin-bottom: 25px;
        }
        .upload-box {
            position: relative;
            border: 2px dashed #204e8a;
            border-radius: 12px;
            background-color: #f8fafc;
            transition: all 0.2s ease;
            text-align: center;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .upload-box:hover {
            background-color: #f1f5f9;
            border-color: #3461a3;
        }
        .upload-content {
            padding: 30px;
        }
        .upload-icon-cloud {
            color: #204e8a;
            font-size: 3.5rem;
            margin-bottom: 10px;
        }
        .upload-title {
            color: #204e8a;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .upload-subtitle {
            color: #6c757d;
            font-size: 13px;
        }
        @media (max-width: 575.98px) {
            .upload-icon-cloud {
                font-size: 2.2rem;
                margin-bottom: 5px;
            }
            .upload-title {
                font-size: 15px;
            }
            .upload-subtitle {
                font-size: 11px;
            }
        }
        .doctor-file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }
        #crop-wrap { 
            display: none; 
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px 0 40px;
            position: relative;
            z-index: 5;
        }
        #crop-container { 
            width: 100%; 
            max-width: 300px; 
            margin: 0 auto; 
        }
        .croppie-container {
            padding-bottom: 15px;
        }
        .cr-slider-wrap {
            margin-bottom: 0 !important; 
            z-index: 10 !important;
        }
        #crop-btn {
            display: none;
            background: #3461a3;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 5px !important;
            position: relative;
            z-index: 10;
        }
        #crop-btn:hover { background: #204e8a; }
        #crop-preview-wrap {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 20px 0;
            z-index: 5;
            position: relative;
        }
        #crop-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #204e8a;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        #recrop-btn {
            background: #ffffff;
            border: 1.5px solid #204e8a;
            color: #204e8a;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        #recrop-btn:hover { background: #204e8a; color: #fff; }
        #discard-btn {
            background: #ffffff;
            border: 1.5px solid #dc3545;
            color: #dc3545;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        #discard-btn:hover { background: #dc3545; color: #fff; }
        .dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .dl-banner { background: #eef2ff; color: #3730a3; border: 1.5px solid #c7d2fe; }
        .dl-banner:hover { background: #e0e7ff; color: #312e81; }
        .dl-video { background: #fffbeb; color: #92400e; border: 1.5px solid #fde68a; }
        .dl-video:hover { background: #fef3c7; color: #78350f; }
        label.error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
            text-transform: none;
            letter-spacing: normal;
        }
        .form-control.error, .form-select.error { border-color: #dc3545; }
        #cropped_image-error {
            text-align: center;
            margin-top: 10px;
        }

        .radio-card {
            flex: 1;
        }
        .radio-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            background-color: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a2e;
            transition: all 0.3s ease;
            width: 100%;
            margin: 0;
        }
        .radio-label i {
            color: #204e8a;
            transition: color 0.3s ease;
        }
        .radio-card input:checked + .radio-label {
            background-color: #204e8a;
            color: #ffffff;
            border-color: #204e8a;
            box-shadow: 0 4px 10px rgba(32, 78, 138, 0.2);
        }
        .radio-card input:checked + .radio-label i {
            color: #ffffff;
        }
        .radio-label:hover {
            border-color: #204e8a;
            background-color: #ffffff;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div>
        <img src="{{ asset('images/logo.png') }}" alt="Zonalta Logo" class="topbar-logo" onerror="this.style.display='none'">
    </div>
    <div class="user-profile">
        <div class="profile-info d-flex align-items-center">
            <div class="avatar-circle me-2">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="user-details me-2 d-none d-sm-block">
                <div class="fw-bold fs-6 text-dark lh-1" style="color: #204e8a !important;">{{ $employee->name }}</div>
                <div class="text-muted small">Employee</div>
            </div>
            <i class="fa-solid fa-chevron-down text-muted small profile-chevron ms-1"></i>
            <div class="profile-dropdown py-2 shadow-lg">
                <ul class="list-unstyled mb-0">
                    <li class="d-block d-sm-none border-bottom mb-2 pb-2">
                        <div class="px-3 fw-bold" style="color: #204e8a; font-size: 15px;">{{ $employee->name }}</div>
                        <div class="px-3 text-muted small">Employee</div>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center text-dark px-3 py-2">
                            <i class="fa-solid fa-arrow-right-from-bracket me-3 text-danger"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-container">
    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 mb-4" style="font-size: 14px; border-radius: 8px;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif
    <div class="custom-card">
        <h3><i class="fa-solid fa-pen-to-square me-2"></i> Create Poster</h3>
        <form id="dashboardForm" method="POST" action="{{ route('poster.store') }}" novalidate>
            @csrf
            <input type="hidden" name="cropped_image" id="cropped_image">
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label" for="name">Doctor Name</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-user-doctor"></i>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Doctor Name" value="">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="msl">MSL Code</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-hashtag"></i>
                        <input type="text" name="msl_code" id="msl" class="form-control" placeholder="Enter MSL Code" value="">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label" for="degree">Degree</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-user-graduate"></i>
                        <input type="text" name="degree" id="degree" class="form-control" placeholder="e.g. MBBS, MD" value="">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="phone">Phone</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" value="" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label" for="address">Address</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-location-dot"></i>
                        <textarea name="address" id="address" class="form-control" placeholder="Full address" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <label class="form-label d-block mb-3">Video Type</label>
                    <div class="d-flex gap-3">
                        <div class="radio-card">
                            <input type="radio" name="video_type" id="bipolar1" value="Bipolar 1" class="d-none">
                            <label for="bipolar1" class="radio-label">
                                 Bipolar 1
                            </label>
                        </div>
                        <div class="radio-card">
                            <input type="radio" name="video_type" id="bipolar2" value="Bipolar 2" class="d-none">
                            <label for="bipolar2" class="radio-label">
                                 Bipolar 2
                            </label>
                        </div>
                    </div>
                    <div id="video_type_error"></div>
                </div>
            </div>
            <div class="photo-section">
                <label class="form-label">Upload Photo</label>
                <div class="upload-box" id="upload-box-wrapper">
                    <div class="upload-content text-center">
                        <i class="fa-solid fa-cloud-arrow-up upload-icon-cloud"></i>
                        <div class="upload-title">Tap to Upload Photo</div>
                        <div class="upload-subtitle">Supports: JPG, PNG</div>
                    </div>
                    <input type="file" id="upload" class="doctor-file-input" accept="image/png, image/jpeg, image/jpg">
                    <div id="crop-wrap">
                        <div id="crop-container"></div>
                        <button type="button" id="crop-btn"><i class="fa-solid fa-crop-simple me-1"></i> Crop & Confirm</button>
                    </div>
                    <div id="crop-preview-wrap">
                        <img id="crop-preview" src="" alt="Cropped photo">
                        <div class="action-buttons mt-2">
                            <button type="button" id="recrop-btn"><i class="fa-solid fa-rotate-right me-1"></i> Re-crop</button>
                            <button type="button" id="discard-btn"><i class="fa-solid fa-trash-can me-1"></i> Remove Photo</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-theme">
                Generate Banner & Video
            </button>
        </form>
    </div>
    @if(isset($poster) && $poster && $poster->banner_path)
        <div class="custom-card">
            <h3><i class="fa-solid fa-download me-2"></i> Download Files</h3>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('download.banner', $poster->id) }}" class="dl-btn dl-banner">
                    <i class="fa-solid fa-image"></i> Download Banner
                </a>
                @if($poster->video_path)
                    <a href="{{ route('download.video', $poster->id) }}" class="dl-btn dl-video">
                        <i class="fa-solid fa-circle-play"></i> Download Video
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>

<script>
    $(document).ready(function () {
        $('.profile-info').on('click', function(e) {
            if(window.innerWidth < 768) {
                e.stopPropagation();
                $(this).toggleClass('active'); 
            }
        });
        $(document).on('click', function() {
            $('.profile-info').removeClass('active');
        });
        $('.profile-dropdown').on('click', function(e) {
            e.stopPropagation(); 
        });
        $("#dashboardForm").validate({
            ignore: "", 
            rules: {
                name: { required: true },
                msl_code: { required: true }, 
                degree: { required: true },
                phone: { 
                    required: true,
                    digits: true,     
                    minlength: 10,
                    maxlength: 10
                },
                address: { required: true }, 
                video_type: { required: true },
                cropped_image: { required: true } 
            },
            messages: {
                name: "Please enter the doctor's name",
                msl_code: "Please enter the MSL code",
                degree: "Please specify the degree",
                phone: {
                    required: "Please enter a phone number",
                    digits: "Only numbers are allowed",
                    minlength: "Phone number should be at least 10 digits",
                    maxlength: "Phone number cannot exceed 10 digits"
                },
                address: "Please enter the full address",
                video_type: "Please select a video type",
                cropped_image: "Please upload and crop a photo"
            },
            errorPlacement: function(error, element) {
                if(element.attr("name") == "cropped_image") {
                    error.insertAfter("#upload-box-wrapper");
                } else if(element.attr("name") == "video_type") {
                    // Radio button ki error naye div me jayegi
                    error.appendTo("#video_type_error"); 
                } else {
                    error.insertAfter(element.closest(".icon-input-wrapper"));
                }
            }
        });
        let cropper = null;
        let originalImageSrc = null; 
        function initCropper(imageSrc) {
            $(".upload-content").hide();
            $("#crop-preview-wrap").hide();
            if (cropper) {
                try { cropper.croppie("destroy"); } catch(err) {}
                cropper = null;
            }
            $("#crop-container").html("");
            $("#crop-wrap").css("display", "flex");
            $("#crop-btn").show();

            let isMobile = window.innerWidth < 576;
            let bWidth = isMobile ? 240 : 300;
            let bHeight = isMobile ? 240 : 300;
            let vWidth = isMobile ? 160 : 200;
            let vHeight = isMobile ? 160 : 200;

            cropper = $("#crop-container").croppie({
                viewport : { width: vWidth, height: vHeight, type: "circle" },
                boundary : { width: bWidth, height: bHeight }
            });
            cropper.croppie("bind", { url: imageSrc });
            $("#upload").prop("disabled", true);
            $(".doctor-file-input").css("pointer-events", "none");
        }
        $("#upload").on("change", function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                originalImageSrc = e.target.result; 
                $("#cropped_image").val(""); 
                initCropper(originalImageSrc);
            };
            reader.readAsDataURL(file);
        });
        $("#crop-btn").on("click", function (e) {
            e.preventDefault(); 
            if (!cropper) return;
            cropper.croppie("result", { type: "base64", size: "viewport", format: "png" })
                .then(function (base64) {
                    $("#cropped_image").val(base64); 
                    $("#crop-wrap").hide();
                    $("#crop-btn").hide();
                    $("#crop-preview").attr("src", base64);
                    $("#crop-preview-wrap").css("display", "flex");
                    $("#dashboardForm").validate().element("#cropped_image");
                });
        });
        $("#recrop-btn").on("click", function () {
            if (originalImageSrc) {
                initCropper(originalImageSrc);
            }
        });
        $("#discard-btn").on("click", function () {
            originalImageSrc = null;
            $("#cropped_image").val("");
            $("#upload").val("");
            $("#upload").prop("disabled", false);
            $(".doctor-file-input").css("pointer-events", "auto");
            $("#crop-preview-wrap").hide();
            $("#crop-wrap").hide();
            $("#crop-btn").hide();
            $(".upload-content").show();
        });
    });
</script>

</body>
</html>