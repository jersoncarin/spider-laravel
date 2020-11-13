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
                        <h1 class="h3 mb-0 text-gray-800">GCASH Accounts</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Total GCASH account : {{ $gcash_count }}
                                <button class="btn btn-primary add btn-sm float-right" data-toggle="modal" data-target="#add-modal"><span class="fas fa-plus"></span> Add Account</button>
                            </h6>
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
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                      @foreach($numbers as $number)
                                        <tr>
                                            <td>{{ $number->account_name }}</td>
                                            <td>{{ $number->account_number }}</td>
                                            <td width="15%">
                                                 <button class="btn btn-success edit btn-sm" data-sets="account_number,account_name" data-url="/admin/gcash/number" data-id="{{  $number->id }}"><span class="fas fa-edit"></span> Edit</button>
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/gcash/number/delete/{{ $number->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{ $numbers->links() }}
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

    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-dark">
            <div class="modal-header">
            <h5 class="modal-title">Add Gcash Account</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body text-dark">

                <form class="edit-form" method="POST" action="/admin/gcash/number/add">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Account Name </label>
                        <input type="text" class="form-control text-dark" name="account_name"  placeholder="Enter account name" required>
                    </div>
                    <div class="form-group">
                        <label>Account Number </label>
                        <input type="number" class="form-control text-dark" name="account_number"  placeholder="Enter account number">
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

     <div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-dark">
            <div class="modal-header">
            <h5 class="modal-title">Edit Gcash Account</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body text-dark">

            <form class="edit-form" method="POST" action="/admin/gcash/number/edit">
            {{ csrf_field() }}
            <input type="hidden" class="hidden-id" name="field_id">
            <div class="form-group">
                <label>Account Name </label>
                <input type="text" class="form-control text-dark" name="account_name"  placeholder="Enter account name" required>
            </div>
            <div class="form-group">
                <label>Account Number </label>
                <input type="number" class="form-control text-dark" name="account_number"  placeholder="Enter account number">
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