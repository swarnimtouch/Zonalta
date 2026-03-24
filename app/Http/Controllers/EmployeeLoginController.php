<?php

namespace App\Http\Controllers;

use App\Models\DoctorPoster;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class EmployeeLoginController extends Controller
{
    public function showLogin()
    {
        return view('employee.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_code' => 'required',
            'password'      => 'required'
        ]);

        if ($request->employee_code !== $request->password) {
            return back()->with('error', 'Invalid credentials');
        }

        $employee = Employee::where('employee_code', $request->employee_code)->first();

        if (!$employee) {
            return back()->with('error', 'Employee not found');
        }

        return redirect()->route('dashboard', $employee->id);
    }

    public function dashboard($id)
    {
        $employee = Employee::findOrFail($id);

        $poster = null;

        if (request()->has('poster_id')) {
            $poster = DoctorPoster::find(request()->poster_id);
        }

        return view('employee.dashboard', compact('employee', 'poster'));
    }

    public function logout()
    {
        return redirect()->route('login');
    }

    // ─────────────────────────────────────────────────────────────────
    //  GENERATE BANNER + VIDEO
    // ─────────────────────────────────────────────────────────────────
    public function storePoster(Request $request, $employee_id)
    {
        $request->validate([
            'name'       => 'required',
            'phone'      => 'required',
            'degree'     => 'required',
            'video_type' => 'required',
        ]);

        $employee      = Employee::findOrFail($employee_id);
        $employee_code = $employee->employee_code;

        $doctorName = $this->cleanName($request->name);

        // ── Photo ──────────────────────────────────────────────────
        $photoPath    = null;   // local temp path (public_path relative)
        $photoS3Path  = null;   // S3 path stored in DB

        if ($request->cropped_image) {
            // Save photo locally (temp) — needed for banner generation
            $localPhotoFolder = $this->makeLocalTempPath('photos');
            $imageName        = $doctorName . '_photo.png';
            $localPhotoFull   = $localPhotoFolder . '/' . $imageName;

            $image = str_replace('data:image/png;base64,', '', $request->cropped_image);
            $image = str_replace(' ', '+', $image);
            file_put_contents($localPhotoFull, base64_decode($image));
            $positionCode = $employee->position_code ?? 'unknown';

            // Upload photo to S3: Zonalta/{emp_id}/photos/
            $s3PhotoKey  = "Zonalta/Employee_{$positionCode}/photos/{$imageName}";
            Storage::disk('s3')->put($s3PhotoKey, file_get_contents($localPhotoFull), 'public');
            $photoS3Path = $s3PhotoKey;

            // Keep local temp path for banner step
            $photoPath = $localPhotoFull;
        }

        // ── Banner ────────────────────────────────────────────────
        [$bannerLocalPath, $bannerS3Path] = $this->generateBanner(
            $request,
            $photoPath,
            $employee_id,
            $doctorName
        );

        // ── Video ─────────────────────────────────────────────────
        [$videoLocalPath, $videoS3Path] = $this->generateVideo(
            $request->video_type,
            $bannerLocalPath,
            $employee_id,
            $doctorName
        );

        // ── Cleanup local temp files ──────────────────────────────
        @unlink($bannerLocalPath);
        @unlink($videoLocalPath);
        if ($photoPath) {
            @unlink($photoPath);
        }

        // ── Save (store S3 paths in DB) ───────────────────────────
        $poster = DoctorPoster::create([
            'employee_id' => $employee_id,
            'name'        => $request->name,
            'msl_code'    => $request->msl_code,
            'degree'      => $request->degree,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'photo'       => $photoS3Path,
            'banner_path' => $bannerS3Path,
            'video_path'  => $videoS3Path,
        ]);

        return redirect()->route('dashboard', [
            'employee'  => $employee_id,
            'poster_id' => $poster->id,
            
        ]);
    }

    // ═════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═════════════════════════════════════════════════════════════════

    /**
     * Create a local TEMP folder under storage/app/temp/
     * (used only during processing — files deleted after S3 upload)
     */
    private function makeLocalTempPath($type)
    {
        $path = storage_path("app/temp/{$type}");
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    private function cleanName($name)
    {
        return strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $name));
    }

    // ─────────────────────────────────────────────────────────────────
    //  BANNER GENERATOR
    // ─────────────────────────────────────────────────────────────────
    /**
     * Returns [localBannerFullPath, s3BannerKey]
     */
    private function generateBanner($request, $photoLocalPath, $employee_id, $doctorName)
    {
        $folder         = $this->makeLocalTempPath('banners');
        $bannerName     = $doctorName . '_banner.jpg';
        $bannerFullPath = $folder . '/' . $bannerName;

        $background = imagecreatefromjpeg(public_path('uploads/base_banner/background.jpg'));

        if ($photoLocalPath && file_exists($photoLocalPath)) {

            $photo = imagecreatefromstring(file_get_contents($photoLocalPath));

            $size   = 250;
            $x      = 300;
            $y      = 300;

            $circle = imagecreatetruecolor($size, $size);
            imagesavealpha($circle, true);
            $trans = imagecolorallocatealpha($circle, 0, 0, 0, 127);
            imagefill($circle, 0, 0, $trans);

            $radius = $size / 2;

            for ($i = 0; $i < $size; $i++) {
                for ($j = 0; $j < $size; $j++) {
                    if ((($i - $radius) ** 2 + ($j - $radius) ** 2) < $radius ** 2) {
                        $color = imagecolorat(
                            $photo,
                            (int)($i * imagesx($photo) / $size),
                            (int)($j * imagesy($photo) / $size)
                        );
                        imagesetpixel($circle, $i, $j, $color);
                    }
                }
            }

            imagecopy($background, $circle, $x, $y, 0, 0, $size, $size);
            imagedestroy($photo);
            imagedestroy($circle);
        }

        $black = imagecolorallocate($background, 0, 0, 0);
        $font  = public_path('fonts/Poppins-Bold.ttf');

        imagettftext($background, 30, 0, 100, 500, $black, $font, $request->name);
        imagettftext($background, 20, 0, 100, 550, $black, $font, "Degree: "  . $request->degree);
        imagettftext($background, 20, 0, 100, 600, $black, $font, "Phone: "   . $request->phone);
        imagettftext($background, 20, 0, 100, 650, $black, $font, "Address: " . $request->address);

        imagejpeg($background, $bannerFullPath, 90);
        imagedestroy($background);

        $positionCode = $employee->position_code ?? 'unknown';

        // Upload banner to S3: Zonalta/{emp_id}/banners/
        $s3Key = "Zonalta/Employee_{$positionCode}/banners/{$bannerName}";
        Storage::disk('s3')->put($s3Key, file_get_contents($bannerFullPath), 'public');

        return [$bannerFullPath, $s3Key];
    }

    // ─────────────────────────────────────────────────────────────────
    //  VIDEO GENERATOR  (AUDIO FIX INCLUDED)
    // ─────────────────────────────────────────────────────────────────
    /**
     * Returns [localVideoFullPath, s3VideoKey]
     *
     * AUDIO FIX:
     *  - Base video ka audio stream map kiya hai (-map 0:a?)
     *  - Banner ke 5 sec ke liye silent audio generate kiya hai (anullsrc)
     *  - Dono audio concat ke saath merge kiya hai
     *  - Agar base video mein audio nahi hai, toh bhi crash nahi hoga (0:a? = optional)
     */
    private function generateVideo($videoType, $bannerLocalPath, $employee_id, $doctorName)
    {
        $folder    = $this->makeLocalTempPath('videos');
        $baseVideo = $videoType === 'Video1'
            ? public_path('uploads/base_videos/Video1.mp4')
            : public_path('uploads/base_videos/Video2.mp4');

        $finalName = $doctorName . '_video.mp4';
        $finalPath = $folder . '/' . $finalName;

        // ── Detect video resolution ───────────────────────────────
        $sizeCmd   = "ffprobe -v error -select_streams v:0 "
            . "-show_entries stream=width,height "
            . "-of csv=p=0 \"{$baseVideo}\" 2>&1";
        $sizeOut   = trim(shell_exec($sizeCmd));
        $sizeParts = explode(',', $sizeOut);
        $vw = isset($sizeParts[0]) && (int)$sizeParts[0] > 0 ? (int)$sizeParts[0] : 1080;
        $vh = isset($sizeParts[1]) && (int)$sizeParts[1] > 0 ? (int)$sizeParts[1] : 1920;

        // ── Detect frame rate ─────────────────────────────────────
        $fpsCmd   = "ffprobe -v error -select_streams v:0 "
            . "-show_entries stream=r_frame_rate "
            . "-of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1";
        $fpsRaw   = trim(shell_exec($fpsCmd));
        $fpsParts = explode('/', $fpsRaw);
        $fps = (count($fpsParts) === 2 && (int)$fpsParts[1] > 0)
            ? round((int)$fpsParts[0] / (int)$fpsParts[1])
            : 25;

        // ── Detect audio sample rate from base video ──────────────
        $arCmd  = "ffprobe -v error -select_streams a:0 "
            . "-show_entries stream=sample_rate "
            . "-of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1";
        $arRaw  = trim(shell_exec($arCmd));
        $ar     = (is_numeric($arRaw) && (int)$arRaw > 0) ? (int)$arRaw : 44100;

        // ── FFmpeg command with AUDIO ─────────────────────────────
        //
        //  Inputs:
        //    0 → base video  (video + audio)
        //    1 → banner image (loop 5 sec)
        //
        //  filter_complex:
        //    [1:v] → scale banner to video size          → [banner_v]
        //    anullsrc → silent audio for 5 sec           → [banner_a]
        //    concat base_video + banner (v+a both)       → [outv][outa]
        //
        $cmd = "ffmpeg -y "
            . "-i \"{$baseVideo}\" "
            . "-loop 1 -t 5 -i \"{$bannerLocalPath}\" "
            . "-filter_complex \""
            .   "[1:v]scale={$vw}:{$vh},setsar=1,fps={$fps}[banner_v];"
            .   "anullsrc=channel_layout=stereo:sample_rate={$ar},atrim=duration=5[banner_a];"
            .   "[0:v][0:a?][banner_v][banner_a]concat=n=2:v=1:a=1[outv][outa]"
            . "\" "
            . "-map \"[outv]\" "
            . "-map \"[outa]\" "
            . "-c:v libx264 -preset fast -crf 23 "
            . "-c:a aac -b:a 192k "
            . "-movflags +faststart "
            . "\"{$finalPath}\" 2>&1";

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            \Log::error('ffmpeg failed', [
                'cmd'    => $cmd,
                'output' => $output,
            ]);
            abort(500, 'Video generation failed: ' . implode("\n", $output));
        }
        $positionCode = $employee->position_code ?? 'unknown';

        // Upload video to S3: Zonalta/{emp_id}/videos/
        $s3Key = "Zonalta/Employee_{$positionCode}/videos/{$finalName}";
        Storage::disk('s3')->put($s3Key, fopen($finalPath, 'r'), 'public');

        return [$finalPath, $s3Key];
    }
    public function downloadBanner($id)
    {
        $poster = DoctorPoster::findOrFail($id);

        if (!$poster->banner_path) {
            abort(404);
        }

        return Storage::disk('s3')->download($poster->banner_path);
    }

    public function downloadVideo($id)
    {
        $poster = DoctorPoster::findOrFail($id);

        if (!$poster->video_path) {
            abort(404);
        }

        return Storage::disk('s3')->download($poster->video_path);
    }
}
