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
                        <h1 class="h3 mb-0 text-gray-800">BET LOGS</h1>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Total Logs : {{ $total_logs }}</h6>
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
                                            <th>Fight no.</th>
                                            <th>Side</th>
                                            <th>Action</th>
                                            <th>Amount</th>
                                            <th>Bet</th>
                                            <th>Balance</th>
                                            <th>Logs Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                      @foreach($logs as $log)
                                        <tr>
                                            <td>{{ $log->username }}</td>
                                            <td>{{ sprintf('#%s',$log->fight_no) }}</td>
                                            <td>{{ ucfirst($log->side) }}</td>
                                            <td>{{ $log->action }}</td>
                                            <td>{{ $log->amount }}</td>
                                            <td>{{ $log->bet }}</td>
                                            <td>{{ $log->balance }}</td>
                                            <td>{{ date("M d h:i:s A",strtotime($log->logs_date)) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{ $logs->links() }}
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