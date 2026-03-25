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

        $background = imagecreatefromjpeg(public_path('uploads/base_banner/background.jpg'));

        if ($photoLocalPath && file_exists($photoLocalPath)) {

            $photo = imagecreatefromstring(file_get_contents($photoLocalPath));

            $size = 250;
            $x    = 300;
            $y    = 300;

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
        $s3Key        = "Zonalta/Employee_{$positionCode}/banners/{$bannerName}";
        Storage::disk('s3')->put($s3Key, file_get_contents($bannerFullPath), 'public');

        return [$bannerFullPath, $s3Key];
    }


    private function generateVideo($videoType, $bannerLocalPath, $employee_id, $doctorName)
    {
        $employee = Employee::findOrFail($employee_id);

        $folder    = $this->makeLocalTempPath('videos');
        $baseVideo = $videoType === 'Video1'
            ? public_path('uploads/base_videos/Video1.mp4')
            : public_path('uploads/base_videos/Video2.mp4');

        $finalName   = $doctorName . '_video.mp4';
        $finalPath   = $folder . '/' . $finalName;
        $bannerVideo = $folder . '/' . $doctorName . '_banner_clip.mp4';
        $concatList  = $folder . '/' . $doctorName . '_concat.txt';

        $sizeOut   = trim(shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=p=0 \"{$baseVideo}\" 2>&1"));
        $sizeParts = explode(',', $sizeOut);
        $vw = isset($sizeParts[0]) && (int)$sizeParts[0] > 0 ? (int)$sizeParts[0] : 1080;
        $vh = isset($sizeParts[1]) && (int)$sizeParts[1] > 0 ? (int)$sizeParts[1] : 1920;

        $fpsRaw   = trim(shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1"));
        $fpsParts = explode('/', $fpsRaw);
        $fps = (count($fpsParts) === 2 && (int)$fpsParts[1] > 0) ? round((int)$fpsParts[0] / (int)$fpsParts[1]) : 25;

        $arRaw = trim(shell_exec("ffprobe -v error -select_streams a:0 -show_entries stream=sample_rate -of default=noprint_wrappers=1:nokey=1 \"{$baseVideo}\" 2>&1"));
        $ar    = (is_numeric($arRaw) && (int)$arRaw > 0) ? (int)$arRaw : 44100;

        $cmd1 = "ffmpeg -y "
            . "-loop 1 -t 5 -i \"{$bannerLocalPath}\" "
            . "-f lavfi -t 5 -i \"anullsrc=channel_layout=stereo:sample_rate={$ar}\" "
            . "-vf \"scale={$vw}:{$vh}:force_original_aspect_ratio=decrease,pad={$vw}:{$vh}:(ow-iw)/2:(oh-ih)/2,setsar=1,fps={$fps}\" "
            . "-c:v libx264 -preset ultrafast -crf 23 "  // ultrafast here — banner is static image
            . "-c:a aac -b:a 192k -shortest "
            . "\"{$bannerVideo}\" 2>&1";

        exec($cmd1, $out1, $rc1);
        if ($rc1 !== 0) {
            \Log::error('ffmpeg step1 failed', ['cmd' => $cmd1, 'output' => $out1]);
            abort(500, 'Banner clip generation failed: ' . implode("\n", $out1));
        }

        file_put_contents($concatList,
            "file '" . addslashes($baseVideo)   . "'\n" .  // ← base video FIRST
            "file '" . addslashes($bannerVideo) . "'\n"    // ← banner LAST
        );


        $cmd2 = "ffmpeg -y "
            . "-f concat -safe 0 -i \"{$concatList}\" "
            . "-c copy "   // ← stream copy everything — ZERO re-encoding of base video
            . "-movflags +faststart "
            . "\"{$finalPath}\" 2>&1";

        exec($cmd2, $out2, $rc2);

        @unlink($bannerVideo);
        @unlink($concatList);

        if ($rc2 !== 0) {
            \Log::error('ffmpeg step2 failed', ['cmd' => $cmd2, 'output' => $out2]);
            abort(500, 'Video concat failed: ' . implode("\n", $out2));
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
