@extends('layouts.app')

@section('content')
    <div class="mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h1 class="text-2xl font-semibold mb-2">Image Management System</h1>
        <p class="text-sm text-slate-600 dark:text-slate-500">
            Welcome to the IMS. Use the navigation to manage patients, staff, upload images, record diagnoses and generate
            bills.<br />
            The system offers secure, role-based access control, audit logging, and strict patient-image linkage. It
            supports standard image formats (JPEG, PNG, DICOM), automatic thumbnailing, metadata extraction and search, and
            validation on upload. Clinical staff can record diagnoses, attach reports, and generate itemized bills;
            administrators can manage users, configure backups and encryption, and view usage and billing reports. A RESTful
            API and responsive frontend enable easy integration and use across devices.
        </p>
    </div>
@endsection