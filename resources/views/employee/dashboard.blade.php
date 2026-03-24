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

        /* Topbar Styling - Light Theme */
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info h2 {
            font-size: 15px;
            font-weight: 600;
            color: #204e8a;
            margin: 0;
        }

        .logout-btn {
            color: #dc3545;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border: 1.5px solid #dc3545;
            padding: 6px 16px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: #dc3545;
            color: #ffffff;
        }

        /* Card Styling to match Login */
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

        .custom-card h3 {
            color: #204e8a;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1.5px solid #e2e8f0;
        }

        /* Form & Input Styling */
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

        /* Adjusted for textarea */
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

        /* Button Styling matching Login */
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

        /* Photo Upload Section */
        .photo-section {
            background: #f8fafc;
            border: 1.5px dashed #204e8a;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .upload-label {
            display: inline-block;
            background: #204e8a;
            color: #fff;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .upload-label:hover { background: #163866; }
        
        #upload { display: none; }
        #crop-wrap { display: none; margin-top: 16px; justify-content: center; }
        #crop-container { width: 300px; height: 300px; margin: 0 auto; }
        
        #crop-btn {
            display: none;
            margin: 15px auto 0;
            background: #28a745;
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        #crop-btn:hover { background: #218838; }

        #crop-preview-wrap {
            display: none;
            margin-top: 15px;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        #crop-preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #204e8a;
        }
        #recrop-btn {
            background: none;
            border: 1.5px solid #204e8a;
            color: #204e8a;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
        }
        #recrop-btn:hover { background: #204e8a; color: #fff; }

        /* Downloads Section Buttons */
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
        .dl-banner { 
            background: #eef2ff; color: #3730a3; border: 1.5px solid #c7d2fe; 
        }
        .dl-banner:hover { background: #e0e7ff; color: #312e81; }
        .dl-video { 
            background: #fffbeb; color: #92400e; border: 1.5px solid #fde68a; 
        }
        .dl-video:hover { background: #fef3c7; color: #78350f; }

        /* Error Styles for Validation */
        label.error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
            text-transform: none;
            letter-spacing: normal;
        }
        .form-control.error, .form-select.error {
            border-color: #dc3545;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div>
        <img src="{{ asset('images/logo.png') }}" alt="Zonalta Logo" class="topbar-logo" onerror="this.style.display='none'">
    </div>
    <div class="user-info">
        <h2><i class="fa-solid fa-hand-wave text-warning me-1"></i> Welcome, {{ $employee->name }}</h2>
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout</a>
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
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" value="">
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
                    <label class="form-label" for="video_type">Video Type</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-video"></i>
                        <select name="video_type" id="video_type" class="form-select">
                            <option value="">Select Video Type</option>
                            <option value="Video1">Video 1</option>
                            <option value="Video2">Video 2</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="photo-section">
                <label class="upload-label" for="upload"><i class="fa-solid fa-image me-2"></i> Choose Photo</label>
                <input type="file" id="upload" accept="image/*">

                <div id="crop-wrap">
                    <div id="crop-container"></div>
                </div>
                <button type="button" id="crop-btn"><i class="fa-solid fa-crop-simple me-1"></i> Crop & Confirm</button>

                <div id="crop-preview-wrap">
                    <img id="crop-preview" src="" alt="Cropped photo">
                    <button type="button" id="recrop-btn"><i class="fa-solid fa-rotate-right me-1"></i> Re-crop</button>
                </div>
            </div>

            <button type="submit" class="btn-theme">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Generate Banner & Video
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
    // ----------------------------------------
    // jQuery Validation Plugin Setup
    // ----------------------------------------
    $(document).ready(function () {
        $("#dashboardForm").validate({
            rules: {
                name: { required: true },
                degree: { required: true },
                phone: { 
                    required: true,
                    digits: true,     // Optional: makes sure only numbers are entered
                    minlength: 10     // Optional: standard mobile length
                },
                video_type: { required: true }
                // address and msl_code were not required in your original code, so keeping them optional. 
                // Add them here if you want them mandatory.
            },
            messages: {
                name: "Please enter the doctor's name",
                degree: "Please specify the degree",
                phone: {
                    required: "Please enter a phone number",
                    digits: "Only numbers are allowed",
                    minlength: "Phone number should be at least 10 digits"
                },
                video_type: "Please select a video type"
            },
            errorPlacement: function(error, element) {
                // Place the error label cleanly under the icon input wrapper
                error.insertAfter(element.closest(".icon-input-wrapper"));
            }
        });
    });

    // ----------------------------------------
    // Croppie Image Editor Setup (Unchanged Logic)
    // ----------------------------------------
    let cropper = null;

    $("#upload").on("change", function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            $("#cropped_image").val("");
            $("#crop-preview-wrap").hide().css("display","");
            $("#crop-preview").attr("src","");

            if (cropper) {
                try { cropper.croppie("destroy"); } catch(err) {}
                cropper = null;
            }

            $("#crop-container").html("");
            $("#crop-wrap").css("display", "flex");
            $("#crop-btn").show();

            cropper = $("#crop-container").croppie({
                viewport : { width: 200, height: 200, type: "circle" },
                boundary : { width: 300, height: 300 }
            });
            cropper.croppie("bind", { url: e.target.result });
        };
        reader.readAsDataURL(file);
    });

    $("#crop-btn").on("click", function () {
        if (!cropper) return;
        cropper.croppie("result", { type: "base64", size: "viewport", format: "png" })
            .then(function (base64) {
                $("#cropped_image").val(base64);
                $("#crop-wrap").hide();
                $("#crop-btn").hide();
                $("#crop-preview").attr("src", base64);
                $("#crop-preview-wrap").css("display", "flex");
            });
    });

    $("#recrop-btn").on("click", function () {
        $("#upload").val("").trigger("click");
    });
</script>

</body>
</html>