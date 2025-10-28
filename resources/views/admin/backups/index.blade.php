@extends('layouts.Admin.app')

@section('content')
<div class="main-content">
    <h3>Database Backups</h3>

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
