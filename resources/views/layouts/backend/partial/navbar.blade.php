<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="javascript:void(0)" class="nav-link"><span
                    style="text-shadow: 2px 1px #558ABB;font-weight:900;font-size:24px">Branch Name: </span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link"><span
                    style="text-shadow: 2px 1px #558ABB;font-weight:900;font-size:24px">{{ Auth::user()->branch->name }}</span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link"><span
                    style="text-shadow: 2px 1px #558ABB;font-weight:900;font-size:24px">Today's Sale: &#8358;
                    {{ number_format(Auth::user()->todaySale(), 2, '.', ',') }}</span></a>
        </li>

    </ul>

    <!-- SEARCH FORM -->
    {{-- <form class="form-inline ml-3" method="post" action="">
        @csrf
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search"
                aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </form> --}}
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <a href="{{ route('chatify') }}" title="Chat System" target="_BLANK"
            class="fa fa-wechat text text-info float-md-right">Chat</a> &nbsp;&nbsp;&nbsp;
        @can('setting.change-branch')
            {{--        @if (Auth::user()->hasRole('Super-admin')) --}}
            <a href="javascript:void(0)" data-toggle="modal" title="Switch Branch" data-target="#swtich_branch"
                class="fa fa-adjust text text-danger float-md-right">Switch Branch</a> &nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" data-toggle="modal" title="Available Stock" data-target="#avail_stock"
                class="fa fa-camera-retro text text-info float-md-right">Stock</a>
            {{--        @endif --}}
        @endcan
        <!-- Profile Dropdown Menu -->
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown"
                href="" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="mdi mdi-bell noti-icon"></i>
                <span class="badge badge-danger badge-pill noti-icon-badge">{{ $expires->count() }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h6 class="m-0"><span class="float-right"><a href="" class="text-dark"><small>Clear
                                    All</small></a> </span>Notification
                    </h6>
                </div>

                <div class="slimscroll" style="max-height: 190px;">
                    <!-- item-->
                    <a href="" class="dropdown-item notify-item">
                        <div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
                        <p class="notify-details">Expired products<small class="text-muted"></small></p>
                    </a>
                    @foreach ($expires as $prod)
                        <a href="">{{ $prod->product->name }} expiring on
                            {{ Carbon\Carbon::parse($prod->expire_date)->toFormattedDateString() }}</a>
                    @endforeach
                </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-th-large"></i>

            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">Profile Menu</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile') }}" class="dropdown-item">
                    <i class="ion-ios-person-outline"></i> Profile
                </a>
                @can('users.index')
                    <a href="{{ route('users.index') }}" class="dropdown-item">
                        <i class="ion-ios-personadd"></i> Manage Users
                    </a>
                @endcan
                @can('notification')
                    <a href="{{ route('notification') }}" class="dropdown-item">
                        <i class=""></i> Notification
                    </a>
                @endcan
                @can('roles.index')
                    <a href="{{ route('roles.index') }}" class="dropdown-item">
                        <i class="ion-ios-locked-outline"></i> Roles
                    </a>
                @endcan
                @can('permissions.index')
                    <a href="{{ route('permissions.index') }}" class="dropdown-item">
                        <i class="ion-ios-locked-outline"></i> Permissions
                    </a>
                @endcan
                @can('role-permission')
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('role-permission') }}" class="dropdown-item">
                        <i class="ion-ios-sunny"></i> Role Permission
                    </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="ion-log-out"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

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
