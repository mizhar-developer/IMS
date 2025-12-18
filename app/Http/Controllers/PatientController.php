<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientServiceInterface;
use App\Services\StorageServiceInterface;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    protected $service;
    protected $storage;

    public function __construct(PatientServiceInterface $service, StorageServiceInterface $storage)
    {
        $this->service = $service;
        $this->storage = $storage;
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $patients = $this->service->list($q);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:10240',
        ]);

        $file = $request->file('profile_picture');
        $createData = $data;
        unset($createData['profile_picture']);

        $patient = $this->service->create($createData);

        if ($file) {
            try {
                $path = $this->storage->storeFile($file, 'profiles/patients');
            } catch (\Throwable $e) {
                // Fallback to default storage facade if the storage service fails
                $path = \Illuminate\Support\Facades\Storage::putFile('profiles/patients', $file);
            }
            $this->service->update($patient->id, ['profile_picture' => $path]);
        }

        return redirect()->route('patients.show', $patient->id)->with('success', 'Patient created');
    }

    public function show($id)
    {
        $patient = $this->service->get($id);
        // If user is a patient, ensure they can only view their own record
        $user = Auth::user();
        if ($user && $user->role === 'patient') {
            if ($user->patient_id !== (int) $id) {
                abort(403);
            }
        }
        if (!$patient)
            abort(404);

        // Paginate related lists via service to keep controller thin
        $images = $this->service->listImages($id, 10);
        $diagnoses = $this->service->listDiagnoses($id, 10);

        return view('patients.show', compact('patient', 'images', 'diagnoses'));
    }

    public function edit($id)
    {
        $patient = $this->service->get($id);
        if (!$patient)
            abort(404);
        return view('patients.create', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:10240',
        ]);

        $file = $request->file('profile_picture');
        $updateData = $data;
        unset($updateData['profile_picture']);

        if ($file) {
            try {
                $path = $this->storage->storeFile($file, 'profiles/patients');
            } catch (\Throwable $e) {
                $path = \Illuminate\Support\Facades\Storage::putFile('profiles/patients', $file);
            }
            $updateData['profile_picture'] = $path;
        }

        $this->service->update($id, $updateData);
        return redirect()->route('patients.show', $id)->with('success', 'Patient updated');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('patients.index')->with('success', 'Patient deleted');
    }
}
