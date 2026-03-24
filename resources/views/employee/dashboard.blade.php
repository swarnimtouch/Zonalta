<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zonalta</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6fb;
            color: #1a1a2e;
            min-height: 100vh;
        }

        .topbar {
            background: #1a1a2e;
            color: #fff;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h2 { font-size: 1.1rem; font-weight: 600; }
        .topbar a {
            color: #a0aec0;
            text-decoration: none;
            font-size: .85rem;
            border: 1px solid #4a5568;
            padding: 5px 14px;
            border-radius: 6px;
            transition: all .2s;
        }
        .topbar a:hover { color: #fff; border-color: #fff; }

        .container { max-width: 820px; margin: 36px auto; padding: 0 16px; }

        .card {
            background: #fff;
            border-radius: 14px;
            padding: 32px 36px;
            box-shadow: 0 2px 20px rgba(0,0,0,.07);
            margin-bottom: 28px;
        }
        .card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f2f7;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .form-row.full { grid-template-columns: 1fr; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label {
            font-size: .78rem;
            font-weight: 600;
            color: #718096;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 13px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .93rem;
            background: #f8fafc;
            transition: border .2s;
            outline: none;
            color: #1a1a2e;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { border-color: #667eea; background: #fff; }
        .form-group textarea { resize: vertical; min-height: 80px; }

        /* photo section */
        .photo-section {
            background: #f8fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .photo-section label.upload-label {
            display: inline-block;
            background: #667eea;
            color: #fff;
            padding: 9px 20px;
            border-radius: 7px;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 600;
            transition: background .2s;
        }
        .photo-section label.upload-label:hover { background: #5a67d8; }
        #upload { display: none; }
        #crop-wrap { display: none; margin-top: 16px; }
        #crop-container { width: 300px; height: 300px; }
        #crop-btn {
            display: none;
            margin-top: 12px;
            background: #48bb78;
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 7px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
        }
        #crop-btn:hover { background: #38a169; }
        #crop-preview-wrap {
            display: none;
            margin-top: 14px;
            align-items: center;
            gap: 14px;
        }
        #crop-preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
        }
        #recrop-btn {
            background: none;
            border: 1.5px solid #667eea;
            color: #667eea;
            padding: 6px 16px;
            border-radius: 7px;
            font-size: .82rem;
            cursor: pointer;
            font-weight: 600;
        }
        #recrop-btn:hover { background: #667eea; color: #fff; }

        /* single generate button */
        .btn-generate {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .4px;
            transition: opacity .2s, transform .15s;
            margin-top: 6px;
        }
        .btn-generate:hover { opacity: .9; transform: translateY(-1px); }

        /* download section */
        .download-card {
            background: #fff;
            border-radius: 14px;
            padding: 28px 36px;
            box-shadow: 0 2px 20px rgba(0,0,0,.07);
            margin-bottom: 28px;
        }
        .download-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f2f7;
        }
        .download-row { display: flex; gap: 16px; flex-wrap: wrap; }
        .dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 9px;
            font-size: .93rem;
            font-weight: 700;
            text-decoration: none;
            transition: opacity .2s, transform .15s;
        }
        .dl-btn:hover { opacity: .88; transform: translateY(-1px); }
        .dl-banner { background: linear-gradient(135deg,#ebf4ff,#dbeafe); color: #2b6cb0; border: 1.5px solid #bee3f8; }
        .dl-video  { background: linear-gradient(135deg,#fefce8,#fef9c3); color: #92400e; border: 1.5px solid #fde68a; }

        .alert-error {
            background: #fff5f5; color: #c53030;
            border: 1.5px solid #fed7d7;
            border-radius: 8px; padding: 12px 18px;
            margin-bottom: 18px; font-size: .9rem;
        }
    </style>
</head>
<body>

<div class="topbar">
    <h2>👋 Welcome, {{ $employee->name }}</h2>
    <a href="{{ route('logout') }}">Logout</a>
</div>

<div class="container">

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h3>Create Poster</h3>

        <form method="POST" action="{{ route('poster.store', $employee->id) }}">
            @csrf
            <input type="hidden" name="cropped_image" id="cropped_image">

            {{-- Name + MSL Code --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text"
                           name="name"
                           id="name"
                           placeholder="Enter Doctor Name"
                           required
                           value="">
                </div>
                <div class="form-group">
                    <label>MSL Code</label>
                    <input type="text"
                           name="msl_code"
                           id="msl"
                           placeholder="Enter MSL Code"
                           value="">
                </div>
            </div>

            {{-- Degree + Phone --}}
            <div class="form-row">
                <div class="form-group">
                    <label>Degree</label>
                    <input type="text"
                           name="degree"
                           id="degree"
                           placeholder="e.g. MBBS, MD"
                           required
                           value="">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text"
                           name="phone"
                           placeholder="Phone Number"
                           required
                           value="">
                </div>
            </div>

            {{-- Address --}}
            <div class="form-row full">
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" placeholder="Full address"></textarea>
                </div>
            </div>

            {{-- Video Type --}}
            <div class="form-row full">
                <div class="form-group">
                    <label>Video Type</label>
                    <select name="video_type" required>
                        <option value="Video1">Video 1</option>
                        <option value="Video2">Video 2</option>
                    </select>
                </div>
            </div>

            {{-- Photo Crop --}}
            <div class="photo-section">
                <label class="upload-label" for="upload"> Choose Photo</label>
                <input type="file" id="upload" accept="image/*">

                <div id="crop-wrap">
                    <div id="crop-container"></div>
                    <button type="button" id="crop-btn"> Crop &amp; Confirm</button>
                </div>

                <div id="crop-preview-wrap">
                    <img id="crop-preview" src="" alt="Cropped photo">
                    <button type="button" id="recrop-btn">Re-crop</button>
                </div>
            </div>

            {{-- Generate button --}}
            <button type="submit" class="btn-generate">
                Generate Banner &amp; Video
            </button>

        </form>
    </div>

    {{-- Download section --}}
        @if(isset($poster) && $poster && $poster->banner_path)
            <div class="download-card">
                <h3>Download Files</h3>
                <div class="download-row">
                    <a href="{{ route('download.banner', $poster->id) }}"
                       class="dl-btn dl-banner">
                        Download Banner
                    </a>

                    @if($poster->video_path)
                        <a href="{{ route('download.video', $poster->id) }}"
                           class="dl-btn dl-video">
                            Download Video
                        </a>
                    @endif
                </div>
            </div>
        @endif


</div>

<script>
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
            $("#crop-wrap").show();
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
