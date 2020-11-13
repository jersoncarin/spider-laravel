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
                        <h1 class="h3 mb-0 text-gray-800">Requests</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Total Deposit Users : {{ $request_count_deposit }}</h6>
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
                                            <th>Amount</th>
                                            <th>Sender Number</th>
                                            <th>Receiver Number</th>
                                            <th>Ref Number</th>
                                            <th>Screenshot</th>
                                            <th>Status</th>
                                            <th>Requested Date</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    @if($requests_deposit)
                                      
                                      @foreach($requests_deposit as $request)

                                        <tr>
                                            <td>{{ $request->username }}</td>
                                            <td>{{ $request->amount }}</td>
                                            <td>{{ $request->sender_number }}</td>
                                            <td>{{ $request->reciever_number }}</td>
                                            <td>{{ $request->reference_number }}</td>
                                            <td><a href="{!! $request->screenshot_path !!}" data-toggle="lightbox" data-title="Receipt Verification"  data-type="image">View Receipt</a>
                                            </td>
                                            <td>{{ $request->status }}</td>
                                            <td>{{ date("M d h:i:s A",strtotime($request->request_date)) }}</td>
                                            <td>
                                               @if($request->status == 'Pending')
                                                 <a class="btn btn-success approve btn-sm" href="/admin/requests/approve/{{ $request->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-edit"></span> Approve</a>
                                               @endif

                                               @if($request->status == 'Rejected')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?delete=true&access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
                                               @elseif($request->status == 'Pending')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-times-circle"></span> Reject</a>
                                               @elseif($request->status == 'Approved')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?delete=true&access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
                                               @endif
                                            </td>
                                        </tr>

                                      @endforeach

                                    @endif

                                    </tbody>
                                </table>

                                {{ $requests_deposit->links() }}
                            </div>
                        </div>
                    </div>

                           <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Total Withdraw Users : {{ $request_count_withdraw }}</h6>
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
                                            <th>Amount</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Contact Number</th>
                                            <th>Status</th>
                                            <th>Requested Date</th>
                                            <th>Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    @if($requests_deposit)
                                      
                                      @foreach($requests_withdraw as $request)

                                        <tr>
                                            <td>{{ $request->username }}</td>
                                            <td>{{ $request->amount }}</td>
                                            <td>{{ $request->account_name }}</td>
                                            <td>{{ $request->account_number }}</td>
                                            <td>{{ $request->contact_number }}</td>
                                            <td>{{ $request->status }}</td>
                                            <td>{{ date("M d h:i:s A",strtotime($request->request_date)) }}</td>
                                            <td>
                                               @if($request->status == 'Pending')
                                                 <a class="btn btn-success approve btn-sm" href="/admin/requests/approve/{{ $request->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-edit"></span> Approve</a>
                                               @endif

                                               @if($request->status == 'Rejected')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?delete=true&access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
                                               @elseif($request->status == 'Pending')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?access_token={{ csrf_token() }}" ><span class="fas fa-times-circle"></span> Reject</a>
                                               @elseif($request->status == 'Approved')
                                                 <a class="btn btn-danger delete btn-sm" href="/admin/requests/deleteorreject/{{ $request->id }}?delete=true&access_token={{ csrf_token() }}" ><span class="fas fa-trash"></span> Delete</a>
                                               @endif
                                            </td>
                                        </tr>

                                      @endforeach

                                    @endif

                                    </tbody>
                                </table>

                                {{ $requests_deposit->links() }}
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js" integrity="sha512-Y2IiVZeaBwXG1wSV7f13plqlmFOx8MdjuHyYFVoYzhyRr3nH/NMDjTBSswijzADdNzMyWNetbLMfOpIPl6Cv9g==" crossorigin="anonymous"></script>

    <script>

        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

    </script>

</body>

</html>