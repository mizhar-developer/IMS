<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageServiceInterface;
use Illuminate\Support\Facades\Log;
use App\Models\MedicalImage;

class ImageController extends Controller
{
    protected $service;

    public function __construct(ImageServiceInterface $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        return view('images.upload');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'file' => 'required|file',
            'type' => 'required|string',
            'uploaded_by' => 'nullable|integer|exists:users,id',
        ]);

        // prefer authenticated user as uploader when available
        if (empty($data['uploaded_by']) && auth()->check()) {
            $data['uploaded_by'] = auth()->id();
        }

        try {
            $image = $this->service->upload($data);
            return redirect()->route('images.index')->with('success', 'Image uploaded');
        } catch (\Throwable $e) {
            Log::error('Image upload failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    public function listForPatient($patientId)
    {
        $images = $this->service->listForPatient($patientId);
        return view('patients.show', compact('images'));
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $images = $this->service->list($q, 10);

        return view('images.index', compact('images'));
    }
}
