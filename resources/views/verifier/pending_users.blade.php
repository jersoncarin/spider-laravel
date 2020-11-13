@include('admin.components.header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('verifier.components.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('verifier.components.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Pending Users</h1>
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

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Total Pending Users : {{ $users_count }}</h6>
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
                                            <th>Phone Number</th>
                                            <th>Referral Code</th>
                                            <th>Credits</th>
                                            <th>Full Name</th>
                                            <th>City</th>
                                            <th>Country</th>
                                            <th>Zip Code</th>
                                            <th>Bio</th>
                                            <th>Registered Date</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                      @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->phone_number }}</td>
                                            <td>{{ $user->referral_code }}</td>
                                            <td>{{ $user->credits }}</td>
                                            <td>{{ strlen( sprintf('%s %s',$user->first_name,$user->last_name )) > 1 ? sprintf('%s %s',$user->first_name,$user->last_name) : 'Not Set' }}</td>
                                            <td>{{ $user->city  ?? 'Not Set' }}</td>
                                            <td>{{ $user->country  ?? 'Not Set' }}</td>
                                            <td>{{ $user->zipcode ?? 'Not Set' }}</td>
                                            <td>{{ $user->bio ?? 'Not set' }}</td>
                                            <td>{{ date("M d h:i:s A",strtotime($user->registered_date)) }}</td>
                                            <td>
                                                 <a class="btn btn-success approve btn-sm" href="/verifier/users/action/approve/{{ $user->parent_id_user }}?access_token={{ csrf_token() }}" ><span class="fas fa-edit"></span> Approve</a>
                                                 <a class="btn btn-danger delete btn-sm" href="/verifier/users/action/delete/{{ $user->parent_id_user }}?access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
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

           @include('verifier.components.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="/frontend/static/js/admin.min.js"></script>

</body>

</html>