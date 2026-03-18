<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom shadow-sm">
    <!-- Left: sidebar toggle -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-md-inline-block">
            <div class="nav-link py-0 d-flex align-items-center">
                <span class="text-muted small mr-1">Branch:</span>
                <span class="font-weight-bold text-primary">{{ optional(Auth::user()->branch)->name ?? '—' }}</span>
            </div>
        </li>
        <li class="nav-item d-none d-lg-inline-block">
            <div class="nav-link py-0 d-flex align-items-center">
                <span class="text-muted small mr-1">Today's sale:</span>
                <span class="font-weight-bold text-success">&#8358;{{ number_format(Auth::user()->todaySale(), 2, '.', ',') }}</span>
            </div>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a href="{{ route('chatify') }}" title="Chat" target="_blank" class="nav-link px-2">
                <i class="fa fa-comments text-info"></i>
                <span class="d-none d-sm-inline ml-1">Chat</span>
            </a>
        </li>
        @can('setting.change-branch')
            <li class="nav-item">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#swtich_branch" title="Switch Branch" class="nav-link px-2">
                    <i class="fa fa-exchange text-warning"></i>
                    <span class="d-none d-sm-inline ml-1">Switch Branch</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#avail_stock" title="Available Stock" class="nav-link px-2">
                    <i class="fa fa-cubes text-info"></i>
                    <span class="d-none d-sm-inline ml-1">Stock</span>
                </a>
            </li>
        @endcan

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell"></i>
                @if($expires->count() > 0)
                    <span class="badge badge-danger badge-pill align-top">{{ $expires->count() }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg border-0 shadow">
                <div class="dropdown-header d-flex justify-content-between align-items-center py-2">
                    <span class="font-weight-bold">Notifications</span>
                </div>
                <div class="dropdown-divider"></div>
                <div style="max-height: 220px; overflow-y: auto;">
                    @forelse($expires as $prod)
                        <a href="#" class="dropdown-item py-2">
                            <small class="text-muted d-block">Expiring {{ Carbon\Carbon::parse($prod->expire_date)->toFormattedDateString() }}</small>
                            {{ $prod->product->name ?? 'Product' }}
                        </a>
                    @empty
                        <span class="dropdown-item text-muted">No expiring products</span>
                    @endforelse
                </div>
            </div>
        </li>

        <!-- User menu -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-user-circle fa-lg text-secondary mr-1"></i>
                <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Account' }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow">
                <span class="dropdown-header text-muted small">Profile Menu</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile') }}" class="dropdown-item">
                    <i class="fa fa-user mr-2 text-muted"></i> Profile
                </a>
                @can('users.index')
                    <a href="{{ route('users.index') }}" class="dropdown-item">
                        <i class="fa fa-users mr-2 text-muted"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.period.index') }}" class="dropdown-item">
                        <i class="fa fa-calendar mr-2 text-muted"></i> Opening/Closing Entry
                    </a>
                @endcan
                @can('notification')
                    <a href="{{ route('notification') }}" class="dropdown-item">
                        <i class="fa fa-cog mr-2 text-muted"></i> Notification
                    </a>
                @endcan
                @can('roles.index')
                    <a href="{{ route('roles.index') }}" class="dropdown-item">
                        <i class="fa fa-id-badge mr-2 text-muted"></i> Roles
                    </a>
                @endcan
                @can('permissions.index')
                    <a href="{{ route('permissions.index') }}" class="dropdown-item">
                        <i class="fa fa-key mr-2 text-muted"></i> Permissions
                    </a>
                @endcan
                @can('role-permission')
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('role-permission') }}" class="dropdown-item">
                        <i class="fa fa-shield mr-2 text-muted"></i> Role Permission
                    </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out mr-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </li>
    </ul>
</nav>
<div class="modal fade" id="swtich_branch" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Switch Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('branch.swtich', Auth::id()) }}">
                    @csrf
                    <input type="hidden" value="{{ Auth::id() }}" name="admin_id" />
                    <div class="form-group">
                        &nbsp;&nbsp;
                        <label for="branch_id">Branch</label>
                        <select class="form-control select2-single" name="branch" id="branch" required>
                            <option value="">Select...</option>
                            @foreach (App\Models\Branch::whereIn('id', auth()->user()->branches->pluck('branch_id')->toArray())->orderBy('name')->get() as $data)
                                <option value="{{ $data->id }}">{{ $data->code }}-{{ $data->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="print" value="print" />
                    <input type="hidden" name="modal" value="modal" />
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                            Close
                        </button>
                        <button type="submit" class="btn btn-info px-3"><i class="fa fa-save"></i> Save
                        </button>
                    </div>
                    @method('post')
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="avail_stock" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Current Stock Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('stock.available') }}" target="_BLANK">
                    @csrf
                    <div class="form-group">
                        &nbsp;&nbsp;
                        <label for="categor_id">Category</label>
                        <select class="form-control select2-single" name="categor_id" id="categor_id" required>
                            <option value="">Select...</option>
                            <option value="all">All</option>
                            @foreach (App\Models\Category::orderBy('name')->get() as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        &nbsp;&nbsp;
                        <label for="store">Store</label>
                        <select class="form-control select2-single" name="store" id="store" required>
                            <option value="">Select...</option>
                            <option value="">Select ...</option>
                            @foreach (App\Models\Store::where('branch_id', App\Models\User::userBranchAction())->orderBy('name')->get() as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        &nbsp;&nbsp;
                        <label for="number">Number</label>
                        <input type="number" name="number" value="1" id="number" class="form-control" />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                            Close
                        </button>
                        <button type="submit" class="btn btn-info px-3"><i class="fa fa-save"></i> Generate
                        </button>
                    </div>
                    @method('post')
                </form>
            </div>
        </div>
    </div>
</div>
