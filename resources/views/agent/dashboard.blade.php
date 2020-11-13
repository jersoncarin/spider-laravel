@include('admin.components.header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('agent.components.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('agent.components.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    @if ($errors->any())

                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                         @foreach ($errors->all() as $error)
                            <strong>{!! $error . '<br>' !!} </strong>
                         @endforeach
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    @endif

                    <div class="row">

                        <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Credits</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(Auth::user()->credits, 2, '.', ',') }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-bill-alt fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                               <!-- Earnings (Monthly) Card Example -->
                               <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Commissions
                                                </div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($total_commission, 2, '.', ',') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Earnings (Monthly) Card Example -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Active Users</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_active_users }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_pending_users }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!-- DataTales Example -->
                     <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Referred Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="overflow:auto!important; padding-right: 1px">
                                <div class="form-group" style="float:right">
                                    <div class="input-group pull-right">
                                        <input type="text" class="search form-control rounded-0" placeholder="What you looking for?" value="{{ app('request')->input('q') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary rounded-0 search-btn" type="button"><i class="fas fa-search"></i></button>
                                        </span>
                                        
                                    </div>
                                </div>
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Credits</th>
                                            <th>User Status</th>
                                            <th>Registered Date</th>
                                            <th width="15%">Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                      @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->credits }}</td>
                                            <td>{{ $user->activation == 0 ? 'Pending' : 'Active'}}
                                            <td>{{ date("M d h:i:s A",strtotime($user->registered_date)) }}</td>
                                            <td>
                                                 <a class="btn btn-success approve btn-sm {{ $user->activation == 1 ? 'disabled' : '' }}" href="/agents/pending/users/action/approve/{{ $user->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-edit"></span> {{ $user->activation == 1 ? 'Approved' : 'Approve' }}</a>
                                                 <button class="btn btn-primary edit btn-sm" data-sets="username" data-url="/agents/transfer/credits/users" data-id="{{ $user->id }} "><span class="fas fa-exchange-alt"></span> Transfer</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                  

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

           @include('agent.components.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-dark">
            <div class="modal-header">
            <h5 class="modal-title">Transfer your points</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body text-dark">

            <form class="edit-form" method="POST" action="/agents/transfer/credits/users/edit">
            {{ csrf_field() }}
            <input type="hidden" class="hidden-id" name="field_id">
            <div class="form-group">
                <label>Username </label>
                <input type="text" class="form-control text-dark" name="username" disabled>
            </div>
            <div class="form-group">
                <label>Amount </label>
                <input type="number" class="form-control text-dark" name="amount"  placeholder="Amount to transfer" required>
            </div>
            <div class="pull-right">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>

            </div>
        </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="/frontend/static/js/admin.min.js"></script>

</body>

</html>