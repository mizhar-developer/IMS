<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StaffServiceInterface;
use App\Services\StorageServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    protected $service;
    protected $storage;

    public function __construct(StaffServiceInterface $service, StorageServiceInterface $storage)
    {
        $this->service = $service;
        $this->storage = $storage;
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $staff = $this->service->list($q);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'role' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:10240',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $file = $request->file('profile_picture');
        $createData = $data;
        unset($createData['profile_picture']);

        // hash password if provided
        if (!empty($data['password'])) {
            $createData['password'] = Hash::make($data['password']);
        }

        $member = $this->service->create($createData);

        if ($file) {
            try {
                $path = $this->storage->storeFile($file, 'profiles/staff');
            } catch (\Throwable $e) {
                $path = Storage::putFile('profiles/staff', $file);
            }
            $this->service->update($member->id, ['profile_picture' => $path]);
        }

        return redirect()->route('staff.index')->with('success', 'Staff added');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('staff.index')->with('success', 'Staff removed');
    }

    public function edit($id)
    {
        $member = $this->service->get($id);
        if (!$member)
            abort(404);
        return view('staff.create', compact('member'));
    }

    public function show($id)
    {
        $member = $this->service->get($id);
        if (!$member) {
            abort(404);
        }

        // optionally include images uploaded by this staff member
        $images = $this->service->listUploadedImages($id, 10);

        return view('staff.show', compact('member', 'images'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'role' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:10240',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $file = $request->file('profile_picture');
        $updateData = $data;
        unset($updateData['profile_picture']);

        // hash password if provided, otherwise don't change
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        } else {
            unset($updateData['password']);
        }

        if ($file) {
            try {
                $path = $this->storage->storeFile($file, 'profiles/staff');
            } catch (\Throwable $e) {
                $path = Storage::putFile('profiles/staff', $file);
            }
            $updateData['profile_picture'] = $path;
        }

        $this->service->update($id, $updateData);
        return redirect()->route('staff.index')->with('success', 'Staff updated');
    }
}
