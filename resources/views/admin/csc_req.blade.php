@include('admin.components.header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('admin.components.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('admin.components.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">CSC Requests</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Total CSC Requests : {{ $csc_request_count }}</h6>
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
                                            <th>Subject Name</th>
                                            <th>Has Reply</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                      @foreach($csc_requests as $csc)
                                        <tr>
                                            <td>{{ $csc->username }}</td>
                                            <td>{{ $csc->subject_name }}</td>
                                            <td>{{ $csc->hasReply == 0 ? 'NO' : 'YES'}}</td>
                                            <td>{{ $csc->status == 0 ? 'OPEN' : 'CLOSE' }}</td>
                                            <td>{{ date("M d h:i:s A",strtotime($csc->created_at)) }}</td>
                                            <td width='15%'>
                                                @if($csc->status == 0)
                                                 <a class="btn btn-danger btn-sm" href="/admin/csc/requests/close/{{ $csc->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-times-circle"></span> Close</a>
                                                @elseif($csc->status == 1)
                                                 <a class="btn btn-success btn-sm" href="/admin/csc/requests/close/{{ $csc->id }}?reopen=true&access_token={{ csrf_token() }}" ><span class="fas fa-edit"></span> Reopen</a>
                                                @endif
                                                 <a class="btn btn-success btn-sm" href="/admin/csc/requests/load/{{ $csc->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-sign-out-alt"></span> View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{ $csc_requests->links() }}
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

           @include('admin.components.footer')
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