<?php

namespace App\Http\Controllers;

use App\Models\DoctorPoster;
use Illuminate\Http\Request;
use App\Models\Employee;

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
        $photoPath = null;

        if ($request->cropped_image) {
            $photoFolder = $this->makePath($employee_code, 'photos');

            $image     = str_replace('data:image/png;base64,', '', $request->cropped_image);
            $image     = str_replace(' ', '+', $image);
            $imageName = $doctorName . '_photo.png';

            file_put_contents($photoFolder . '/' . $imageName, base64_decode($image));

            $photoPath = "uploads/{$employee_code}/photos/" . $imageName;
        }

        // ── Banner ────────────────────────────────────────────────
        $bannerPath = $this->generateBanner($request, $photoPath, $employee_code, $doctorName);

        // ── Video ─────────────────────────────────────────────────
        $videoPath = $this->generateVideo(
            $request->video_type,
            $bannerPath,
            $employee_code,
            $doctorName
        );

        // ── Save ──────────────────────────────────────────────────
        $poster = DoctorPoster::create(
            ['employee_id' => $employee_id,
                'name'        => $request->name,
                'msl_code'    => $request->msl_code,
                'degree'      => $request->degree,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'photo'       => $photoPath,
                'banner_path' => $bannerPath,
                'video_path'  => $videoPath,
            ]
        );

        return redirect()->route('dashboard', [
            'employee'  => $employee_id,
            'poster_id' => $poster->id,
        ]);
    }

    // ═════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═════════════════════════════════════════════════════════════════

    private function makePath($employee_code, $type)
    {
        $path = public_path("uploads/{$employee_code}/{$type}");
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
    //  BANNER GENERATOR — no Doctor model dependency
    // ─────────────────────────────────────────────────────────────────
    private function generateBanner($request, $photoPath, $employee_code, $doctorName)
    {
        $folder         = $this->makePath($employee_code, 'banners');
        $bannerName     = $doctorName . '_banner.jpg';
        $bannerFullPath = $folder . '/' . $bannerName;

        $background = imagecreatefromjpeg(public_path('uploads/base_banner/background.jpg'));

        if ($photoPath && file_exists(public_path($photoPath))) {

            $photo = imagecreatefromstring(file_get_contents(public_path($photoPath)));

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

        return "uploads/{$employee_code}/banners/" . $bannerName;
    }

    // ─────────────────────────────────────────────────────────────────
    //  VIDEO GENERATOR
    // ─────────────────────────────────────────────────────────────────
    private function generateVideo($videoType, $bannerPath, $employee_code, $doctorName)
    {
        $folder    = $this->makePath($employee_code, 'videos');
        $baseVideo = $videoType === 'Video1'
            ? public_path('uploads/base_videos/Video1.mp4')
            : public_path('uploads/base_videos/Video2.mp4');

        $finalName  = $doctorName . '_video.mp4';
        $finalPath  = $folder . '/' . $finalName;
        $bannerFull = public_path($bannerPath);

        // ── Detect video resolution ───────────────────────────────
        $sizeCmd   = "ffprobe -v error -select_streams v:0 "
            . "-show_entries stream=width,height "
            . "-of csv=p=0 \"{$baseVideo}\" 2>&1";
        $sizeOut   = trim(shell_exec($sizeCmd));
        $sizeParts = explode(',', $sizeOut);
        $vw = isset($sizeParts[0]) && (int)$sizeParts[0] > 0 ? (int)$sizeParts[0] : 1080;
        $vh = isset($sizeParts[1]) && (int)$sizeParts[1] > 0 ? (int)$sizeParts[1] : 1920;

        // ── Detect frame rate ─────────────────────────────────────
        $fpsCmd = "ffprobe -v error -select_streams v:0 "
            . "-show_entries stream=r_frame_rate "
            . "-of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1";
        $fpsRaw   = trim(shell_exec($fpsCmd));
        $fpsParts = explode('/', $fpsRaw);
        $fps = (count($fpsParts) === 2 && (int)$fpsParts[1] > 0)
            ? round((int)$fpsParts[0] / (int)$fpsParts[1])
            : 25;

        // ── FFmpeg: full video → 5 sec banner appended ────────────
        $cmd = "ffmpeg -y "
            . "-i \"{$baseVideo}\" "
            . "-loop 1 -t 5 -i \"{$bannerFull}\" "
            . "-filter_complex \""
            .   "[1:v]scale={$vw}:{$vh},setsar=1,fps={$fps}[banner5sec];"
            .   "[0:v][banner5sec]concat=n=2:v=1:a=0[outv]"
            . "\" "
            . "-map \"[outv]\" "
            . "-c:v libx264 -preset fast -crf 23 "
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

        return "uploads/{$employee_code}/videos/" . $finalName;
    }
}
