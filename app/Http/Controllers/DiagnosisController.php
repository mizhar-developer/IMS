<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\DiagnosisServiceInterface;
use App\Services\ImageServiceInterface;
use App\Models\Diagnosis;
use App\Models\DiagnosisComment;

class DiagnosisController extends Controller
{
    protected $service;
    protected $imageService;

    public function __construct(DiagnosisServiceInterface $service, ImageServiceInterface $imageService)
    {
        $this->service = $service;
        $this->imageService = $imageService;
    }

    public function create()
    {
        return view('diagnoses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            // 'image_id' => 'nullable|integer|exists:medical_images,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp',
            'image_type' => 'nullable|string',
            'doctor_id' => 'nullable|integer|exists:users,id',
            'disease_type' => 'required|string',
            'report' => 'nullable|string',
            'confidence' => 'nullable|numeric',
        ]);

        // default doctor to the authenticated user when not provided
        if (empty($data['doctor_id']) && auth()->check()) {
            $data['doctor_id'] = auth()->id();
        }

        $diagnosis = $this->service->create($data);

        // handle multiple uploaded images and link them to this diagnosis
        $firstImageId = null;
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                if (!$file->isValid())
                    continue;
                try {
                    $img = $this->imageService->upload([
                        'file' => $file,
                        'patient_id' => $data['patient_id'],
                        'uploaded_by' => auth()->id() ?? null,
                        'type' => $data['image_type'] ?? 'diagnosis',
                        'diagnosis_id' => $diagnosis->id,
                    ]);
                    if ($firstImageId === null)
                        $firstImageId = $img->id;
                } catch (\Throwable $e) {
                    Log::error('Diagnosis image upload failed: ' . $e->getMessage());
                    // continue with other files
                }
            }
        }

        // if no image_id set on diagnosis, set to first uploaded image
        // if (!$diagnosis->image_id && $firstImageId) {
        //     $diagnosis->image_id = $firstImageId;
        //     $diagnosis->save();
        // }

        return redirect()->route('diagnoses.index')->with('success', 'Diagnosis saved');
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $diagnoses = $this->service->list($q, 10);

        return view('diagnoses.index', compact('diagnoses'));
    }

    public function show(Request $request, $id)
    {
        $diagnosis = $this->service->get($id);
        if (!$diagnosis)
            abort(404);

        // Authorization: patients can only view their own diagnosis
        $user = Auth::user();
        if ($user && isset($user->role) && $user->role === 'patient') {
            if (isset($user->patient_id) && $user->patient_id != $diagnosis->patient_id) {
                abort(403);
            }
        }

        $comments = DiagnosisComment::where('diagnosis_id', $id)->orderBy('created_at', 'asc')->get();

        return view('diagnoses.show', compact('diagnosis', 'comments'));
    }

    public function edit($id)
    {
        $diagnosis = $this->service->get($id);
        if (!$diagnosis)
            abort(404);
        return view('diagnoses.edit', compact('diagnosis'));
    }

    public function update(Request $request, $id)
    {
        $diagnosis = $this->service->get($id);
        if (!$diagnosis)
            abort(404);

        $data = $request->validate([
            'doctor_id' => 'nullable|integer|exists:users,id',
            // 'image_id' => 'nullable|integer|exists:medical_images,id',
            'disease_type' => 'required|string',
            'report' => 'nullable|string',
            'confidence' => 'nullable|numeric',
        ]);

        // default doctor to authenticated user if not provided
        if (empty($data['doctor_id']) && auth()->check()) {
            $data['doctor_id'] = auth()->id();
        }

        $updated = $this->service->update($id, $data);
        if (!$updated)
            return back()->with('error', 'Unable to update diagnosis');
        return redirect()->route('diagnoses.show', $id)->with('success', 'Diagnosis updated');
    }

    public function storeComment(Request $request, $id)
    {
        $diagnosis = Diagnosis::find($id);
        if (!$diagnosis)
            abort(404);

        $data = $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file',
        ]);

        $user = Auth::user();
        $userType = $user->role ?? 'user';
        $userId = $user->id ?? null;

        $imageId = null;
        if ($request->hasFile('file')) {
            try {
                $img = $this->imageService->upload([
                    'file' => $request->file('file'),
                    'patient_id' => $diagnosis->patient_id,
                    'uploaded_by' => auth()->id() ?? null,
                    'type' => 'comment',
                    'diagnosis_id' => $diagnosis->id,
                ]);
                $imageId = $img->id;
            } catch (\Throwable $e) {
                Log::error('Comment image upload failed: ' . $e->getMessage());
                return back()->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        DiagnosisComment::create([
            'diagnosis_id' => $diagnosis->id,
            'user_type' => $userType,
            'user_id' => $userId,
            'content' => $data['content'] ?? null,
            'image_id' => $imageId,
        ]);

        return redirect()->route('diagnoses.show', $id)->with('success', 'Comment saved');
    }

    public function destroy($id)
    {
        $diagnosis = $this->service->get($id);
        if (!$diagnosis)
            return back()->with('error', 'Diagnosis not found');

        // Only allow deletion by doctors, radiologists or admin via route middleware
        try {
            $ok = $this->service->delete($id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to delete diagnosis');
        }

        return redirect()->route('diagnoses.index')->with('success', 'Diagnosis deleted');
    }
}
