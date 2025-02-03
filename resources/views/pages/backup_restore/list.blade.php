@extends('layouts.backend.app')

@section('title', 'Bank Branch')

@push('css')
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Backup & Restore</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Backup</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('log'))
                    <div class="alert alert-info">
                        <h4>Backup Log</h4>
                        <pre style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd;">{!! session('log') !!}</pre>
                    </div>
                @endif

                <a href="{{ route('backup.create') }}" class="btn btn-success">Create Backup</a>

                <h5>Available Backups</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <td>{{ basename($file) }}</td>
                                <td>
                                    <a href="{{ route('backup.download', ['file' => basename($file)]) }}"
                                        class="btn btn-primary">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Restore Database</h3>
                <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="backup_file" required>
                    <button type="submit" class="btn btn-danger">Restore</button>
                </form>
            </div>

    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading state to backup button
            const backupBtn = document.querySelector('a[href="{{ route('backup.create') }}"]');
            if (backupBtn) {
                backupBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (backupBtn.classList.contains('disabled')) {
                        return;
                    }

                    backupBtn.classList.add('disabled');
                    backupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Backup...';

                    // Make the backup request
                    fetch('{{ route('backup.create') }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Show success message
                                const alert = document.createElement('div');
                                alert.className = 'alert alert-success';
                                alert.textContent = data.message;
                                document.querySelector('.container-fluid').insertBefore(alert, document
                                    .querySelector('.table'));

                                // Reload page after 2 seconds
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            // Show error message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-danger';
                            alert.textContent = error.message;
                            document.querySelector('.container-fluid').insertBefore(alert, document
                                .querySelector('.table'));

                            // Reset button
                            backupBtn.classList.remove('disabled');
                            backupBtn.textContent = 'Create Backup';
                        });
                });
            }

            // Add confirmation to restore
            const restoreForm = document.querySelector('form[action="{{ route('backup.restore') }}"]');
            if (restoreForm) {
                restoreForm.addEventListener('submit', function(e) {
                    if (!confirm(
                            'Warning: This will overwrite your current database. Are you sure you want to proceed?'
                        )) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endpush
