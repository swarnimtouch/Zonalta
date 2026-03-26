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

        session(['employee_id' => $employee->id]);

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $employee_id = session('employee_id');

        if (!$employee_id) {
            return redirect()->route('login');
        }

        $employee = Employee::findOrFail($employee_id);

        $poster = null;
        // flash session se poster_id milega — sirf ek baar
        if (session('poster_id')) {
            $poster = DoctorPoster::find(session('poster_id'));
        }

        return view('employee.dashboard', compact('employee', 'poster'));
    }


    public function logout()
    {
        session()->forget(['employee_id', 'poster_id']);
        return redirect()->route('login');
    }


    public function storePoster(Request $request)
    {
        $employee_id = session('employee_id');

        if (!$employee_id) {
            return redirect()->route('login');
        }

        $request->validate([
            'name'       => 'required',
            'phone'      => 'required',
            'degree'     => 'required',
            'video_type' => 'required',
        ]);

        $employee      = Employee::findOrFail($employee_id);
        $employee_code = $employee->employee_code;

        $doctorName = $this->cleanName($request->name);

        $photoPath   = null;
        $photoS3Path = null;

        if ($request->cropped_image) {
            $localPhotoFolder = $this->makeLocalTempPath('photos');
            $imageName        = $doctorName . '_photo.png';
            $localPhotoFull   = $localPhotoFolder . '/' . $imageName;

            $image = str_replace('data:image/png;base64,', '', $request->cropped_image);
            $image = str_replace(' ', '+', $image);
            file_put_contents($localPhotoFull, base64_decode($image));

            $positionCode = $employee->position_code ?? 'unknown';
            $s3PhotoKey   = "Zonalta/Employee_{$positionCode}/photos/{$imageName}";
            Storage::disk('s3')->put($s3PhotoKey, file_get_contents($localPhotoFull), 'public');
            $photoS3Path = $s3PhotoKey;
            $photoPath   = $localPhotoFull;
        }

        [$bannerLocalPath, $bannerS3Path] = $this->generateBanner(
            $request,
            $photoPath,
            $employee_id,
            $doctorName
        );

        [$videoLocalPath, $videoS3Path] = $this->generateVideo(
            $request->video_type,
            $bannerLocalPath,
            $employee_id,
            $doctorName
        );

        @unlink($bannerLocalPath);
        @unlink($videoLocalPath);
        if ($photoPath) {
            @unlink($photoPath);
        }

        $poster = DoctorPoster::create([
            'employee_id' => $employee_id,
            'prefix'      => $request->prefix,
            'name'        => $request->name,
            'msl_code'    => $request->msl_code,
            'degree'      => $request->degree,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'photo'       => $photoS3Path,
            'banner_path' => $bannerS3Path,
            'video_path'  => $videoS3Path,
        ]);

        session()->flash('poster_id', $poster->id);


        return redirect()->route('dashboard');
    }

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

    private function generateBanner($request, $photoLocalPath, $employee_id, $doctorName)
    {
        $employee = Employee::findOrFail($employee_id);

        $folder         = $this->makeLocalTempPath('banners');
        $bannerName     = $doctorName . '_banner.jpg';
        $bannerFullPath = $folder . '/' . $bannerName;

        // Load background (1080x1080)
        $background = imagecreatefromjpeg(public_path('uploads/base_banner/background.jpg'));

        // ── PHOTO (circle crop) ──────────────────────────────────────────
        // From transform panel: X=291, Y=174, W=502, H=502
        if ($photoLocalPath && file_exists($photoLocalPath)) {

            $photo = imagecreatefromstring(file_get_contents($photoLocalPath));
            $srcW  = imagesx($photo);
            $srcH  = imagesy($photo);

            $size  = 502;
            $destX = 291;
            $destY = 174;

            // ✅ Step 1: Photo ko square crop karo (center se) — aspect ratio preserve
            $squareSize = min($srcW, $srcH);
            $cropX      = (int)(($srcW - $squareSize) / 2);
            $cropY      = (int)(($srcH - $squareSize) / 2);

            $squared = imagecreatetruecolor($size, $size);
            imagecopyresampled(
                $squared, $photo,
                0, 0,
                $cropX, $cropY,
                $size, $size,
                $squareSize, $squareSize
            );
            imagedestroy($photo);

            // ✅ Step 2: Circle mask — imagecopyresampled se sharp image pe apply karo
            $circle = imagecreatetruecolor($size, $size);
            imagealphablending($circle, false);
            imagesavealpha($circle, true);
            $trans  = imagecolorallocatealpha($circle, 0, 0, 0, 127);
            imagefill($circle, 0, 0, $trans);

            $radius = $size / 2;
            for ($i = 0; $i < $size; $i++) {
                for ($j = 0; $j < $size; $j++) {
                    if ((($i - $radius) ** 2 + ($j - $radius) ** 2) < $radius ** 2) {
                        $color = imagecolorat($squared, $i, $j);
                        imagesetpixel($circle, $i, $j, $color);
                    }
                }
            }
            imagedestroy($squared);

            // ✅ Step 3: Background pe paste karo
            imagealphablending($background, true);
            imagecopy($background, $circle, $destX, $destY, 0, 0, $size, $size);
            imagedestroy($circle);
        }

        // ── TEXT ─────────────────────────────────────────────────────────
        $blue     = imagecolorallocate($background, 8, 85, 165); // #0855a5
        $fontBold = public_path('fonts/Baskervville-SemiBold.ttf');
        $fontReg  = public_path('fonts/Baskervville-Regular.ttf');

        $startX = 0;
        $endX   = 1080;

        // Helper: center text horizontally between startX and endX
        $centerText = function($bg, $size, $angle, $font, $text, $y) use ($startX, $endX, $blue) {
            $box = imagettfbbox($size, $angle, $font, $text);
            $tw  = abs($box[2] - $box[0]);
            $x   = $startX + (($endX - $startX) - $tw) / 2;
            imagettftext($bg, $size, $angle, (int)$x, $y, $blue, $font, $text);
        };

        // NAME — SemiBold, centered, Y=2205
        $fullName = trim(($request->prefix ? $request->prefix . ' ' : '') . $request->name);
        $nameY = 770;
        $centerText($background, 36, 0, $fontBold, $fullName, $nameY);


        // DEGREE — Regular, below name
        $degreeY = $nameY + 55;
        $centerText($background, 24, 0, $fontReg, $request->degree, $degreeY);



        // ADDRESS — conditional line wrapping (max 3 lines)
        $address  = $request->address ?? '';
        $maxChars = 35;
        $words    = explode(' ', $address);
        $lines    = [];
        $line     = '';

        foreach ($words as $word) {
            $test = $line === '' ? $word : $line . ' ' . $word;
            if (strlen($test) > $maxChars && $line !== '') {
                $lines[] = $line;
                $line    = $word;
            } else {
                $line = $test;
            }
        }
        if ($line !== '') $lines[] = $line;

        // Cap at 3 lines, truncate with '...' if exceeded
        if (count($lines) > 3) {
            $lines    = array_slice($lines, 0, 3);
            $lines[2] .= '...';
        }

        $addrFontSize = 20;
        $addrLineH    = 36;
        $addrStartY   = $degreeY+ 50;

        foreach ($lines as $i => $addrLine) {
            $centerText(
                $background,
                $addrFontSize,
                0,
                $fontReg,
                $addrLine,
                $addrStartY + ($i * $addrLineH)
            );
        }
        // PHONE — Regular, below degree
        $phoneY = $addrStartY + (count($lines) * $addrLineH) + 15;

        $centerText($background, 24, 0, $fontReg, $request->phone, $phoneY);
        // ── SAVE & UPLOAD ─────────────────────────────────────────────────
        imagejpeg($background, $bannerFullPath, 90);
        imagedestroy($background);

        $positionCode = $employee->position_code ?? 'unknown';
        $s3Key        = "Zonalta/Employee_{$positionCode}/banners/{$bannerName}";
        Storage::disk('s3')->put($s3Key, file_get_contents($bannerFullPath), 'public');

        return [$bannerFullPath, $s3Key];
    }


    private function generateVideo($videoType, $bannerLocalPath, $employee_id, $doctorName)
    {
        $employee = Employee::findOrFail($employee_id);

        $folder    = $this->makeLocalTempPath('videos');
        $videoType = strtolower(trim($videoType));

        $videoMap = [
            'video1' => 'Video1.mp4',
            'video2' => 'Video2.mp4',
        ];

// default fallback = Video1
        $videoFile = $videoMap[$videoType] ?? 'Video1.mp4';

        $baseVideo = public_path('uploads/base_videos/' . $videoFile);

        $finalName   = $doctorName . '_video.mp4';
        $finalPath   = $folder . '/' . $finalName;
        $bannerVideo = $folder . '/' . $doctorName . '_banner_clip.mp4';
        $concatList  = $folder . '/' . $doctorName . '_concat.txt';

        // Base video ka size/fps/audio detect karo
        $sizeOut   = trim(shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=p=0 \"{$baseVideo}\" 2>&1"));
        $sizeParts = explode(',', $sizeOut);
        $vw = isset($sizeParts[0]) && (int)$sizeParts[0] > 0 ? (int)$sizeParts[0] : 1080;
        $vh = isset($sizeParts[1]) && (int)$sizeParts[1] > 0 ? (int)$sizeParts[1] : 1920;

        $fpsRaw   = trim(shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1"));
        $fpsParts = explode('/', $fpsRaw);
        $fps = (count($fpsParts) === 2 && (int)$fpsParts[1] > 0) ? round((int)$fpsParts[0] / (int)$fpsParts[1]) : 25;

        $arRaw = trim(shell_exec("ffprobe -v error -select_streams a:0 -show_entries stream=sample_rate -of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1"));
        $ar    = (is_numeric($arRaw) && (int)$arRaw > 0) ? (int)$arRaw : 44100;

        // ✅ FIX 1: pix_fmt yuv420p add kiya — base video se match karega
        // ✅ FIX 2: audio ko -ar se sample rate explicitly set kiya
        $cmd1 = "ffmpeg -y "
            . "-loop 1 -t 10 -i \"{$bannerLocalPath}\" "
            . "-f lavfi -t 10 -i \"anullsrc=channel_layout=stereo:sample_rate={$ar}\" "
            . "-vf \"scale={$vw}:{$vh}:force_original_aspect_ratio=decrease,"
            .       "pad={$vw}:{$vh}:(ow-iw)/2:(oh-ih)/2:color=black,setsar=1,fps={$fps}\" "
            . "-c:v libx264 -preset ultrafast -crf 23 "
            . "-pix_fmt yuv420p "
            . "-c:a aac -ar {$ar} -b:a 128k "
            . "-t 10 "
            . "\"{$bannerVideo}\" 2>&1";

        exec($cmd1, $out1, $rc1);



        if ($rc1 !== 0 || !file_exists($bannerVideo) || filesize($bannerVideo) === 0) {
            \Log::error('ffmpeg step1 failed', ['cmd' => $cmd1, 'output' => $out1]);
            abort(500, 'Banner clip generation failed');
        }

        file_put_contents($concatList,
            "file '" . str_replace("'", "'\\''", $baseVideo)   . "'\n" .
            "file '" . str_replace("'", "'\\''", $bannerVideo) . "'\n"
        );

        // ✅ FIX 3: -c copy ki jagah re-encode — codec/format mismatch resolve hoga
        $cmd2 = "ffmpeg -y "
            . "-f concat -safe 0 -i \"{$concatList}\" "
            . "-c copy "                // ✅ zero re-encoding
            . "-movflags +faststart "
            . "\"{$finalPath}\" 2>&1";


        exec($cmd2, $out2, $rc2);



        @unlink($bannerVideo);
        @unlink($concatList);

        if ($rc2 !== 0 || !file_exists($finalPath) || filesize($finalPath) === 0) {
            \Log::error('ffmpeg step2 failed', ['cmd' => $cmd2, 'output' => $out2]);
            abort(500, 'Video concat failed');
        }

        $positionCode = $employee->position_code ?? 'unknown';
        $s3Key        = "Zonalta/Employee_{$positionCode}/videos/{$finalName}";
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
