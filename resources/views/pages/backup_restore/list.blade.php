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

                <a href="javascript:void(0)" class="btn btn-sm btn-success" id="backupBtn">
                    <span class="fa fa-database"></span> Create Backup
                </a>

                <h5 class="mt-2">Available Backups</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <td>{{ basename($file) }} ({{ number_format(filesize($file) / 1048576, 2) }} MB)</td>
                                <td>
                                    <a href="{{ route('backup.download', ['file' => basename($file)]) }}"
                                        class="btn btn-sm btn-primary"><span class="fa fa-download"></span> Download</a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('backup.delete', ['file' => basename($file)]) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this backup?');">
                                            <span class="fa fa-remove"></span> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <h3>Restore Database</h3>
                <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="backup_file" required accept=".sql">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <span class="fa fa-undo"></span> Restore
                    </button>
                </form>
            </div>

    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('backupBtn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Creating Backup...',
                text: 'Please wait.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('backup.create') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Failed!',
                        text: error.message || 'An error occurred.'
                    });
                });
        });
    </script>
@endpush
