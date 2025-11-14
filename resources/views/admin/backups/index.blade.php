@extends('layouts.Admin.app')

@section('content')
<div class="main-content">
    <h3>Database Backups</h3>

    {{-- Two-level backup explanation for users --}}
    <div class="alert alert-info small">
        <strong>Backup strategy (two-level)</strong>
        <div class="mt-1">We protect your data with two backup levels so you always have a recent copy and an option to keep an offsite archive:</div>
        <ol class="mb-0 mt-1">
            <li><strong>Automatic monthly backup (Primary):</strong> the system automatically creates a database dump every month and stores it on the server. This gives you a regularly updated primary copy without manual action.</li>
            <li><strong>Manual download (Secondary):</strong> you can create and download a backup on demand by clicking <em>Create Manual Backup</em>. Downloaded files are yours to store offsite (external drive, cloud storage, etc.) for extra protection and faster recovery.</li>
        </ol>
        <div class="mt-1">Recommendation: rely on the automatic monthly backups for regular protection, and periodically download important backups to keep an offsite copy.</div>
    </div>

    <div id="alert-area"></div>

    <button id="backupBtn" class="btn btn-primary mb-3">
        <i class="bi bi-download"></i> Create Manual Backup
    </button>

    <h5>Previous Backups</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            @foreach($files as $file)
                <tr>
                    <td>{{ $file['name'] }}</td>
                    <td>
                        <a href="{{ route('admin.backup.download', $file['name']) }}" class="btn btn-success btn-sm">Download</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.getElementById('backupBtn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating Backup...';

    fetch('{{ route('admin.backup.manual') }}')
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-download"></i> Create Manual Backup';

            const alertArea = document.getElementById('alert-area');
            alertArea.innerHTML = '';

            if (data.success) {
                // Show success and auto-download
                alertArea.innerHTML = `<div class="alert alert-success">Backup created successfully! Downloading...</div>`;
                window.location.href = data.download_url;
            } else {
                alertArea.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-download"></i> Create Manual Backup';
            document.getElementById('alert-area').innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
});
</script>
@endsection
