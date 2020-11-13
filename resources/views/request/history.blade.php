@include('components.header')

<body>

  <div class="wrapper">
    <div class="sidebar">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red"
    -->
     @include('components.sidebar')
    <div class="main-panel">
      <!-- Navbar -->
      @include('components.navbar')

      <!-- End Navbar -->
      <div class="content">
        <div class="row">
        <!-- start -->

        <div class="col-md-12">
            <div class="card ">
              <div class="card-header mb-0">
                <h4 class="card-title">{{ ucfirst($request_type . ' History') }}</h4>
              </div>
              <hr class="mt-0">
              <div class="card-body">
                  
                <div class="table-responsive mb-2">
                    <table class='table table-striped'>

                    @if($request_type == 'deposit')
                        <thead class="text-primary">
                          <tr>
                              <th class="text-center">Sender Number</th>
                              <th class="text-center">Receiver Number</th>
                              <th class="text-center">Amount</th>
                              <th class="text-center">Referrence Number</th>
                              <th class="text-center">Status</th>
                              <th class="text-center">Date</th>
                          </tr>
                        </thead>
                        <tbody>

                            @forelse($histories as $history)

                                <tr>
                                    <td>{{ $history->sender_number }}</td>
                                    <td>{{ $history->reciever_number }}</td>
                                    <td>{{ $history->amount }}</td>
                                    <td>{{ $history->reference_number }}</td>
                                    <td>{{ $history->status }}</td>
                                    <td>{{ date('F j Y h:i A', strtotime($history->request_date)) }}</td>
                                </tr>

                            @empty

                                <p>No History found!</p>

                            @endforelse

                        </tbody>
                      @elseif($request_type == 'withdraw')

                      <thead class="text-primary">
                          <tr>
                              <th class="text-center">Account Name</th>
                              <th class="text-center">Account Number</th>
                              <th class="text-center">Amount</th>
                              <th class="text-center">Status</th>
                              <th class="text-center">Date</th>
                          </tr>
                        </thead>
                        <tbody>

                            @forelse($histories as $history)

                                <tr>
                                    <td>{{ $history->account_name }}</td>
                                    <td>{{ $history->account_number }}</td>
                                    <td>{{ $history->amount }}</td>
                                    <td>{{ $history->status }}</td>
                                    <td>{{ date('F j Y h:i A', strtotime($history->request_date)) }}</td>
                                </tr>

                            @empty

                                <p>No History found!</p>

                            @endforelse

                      </tbody>
                      @endif

                     </table>
                    </div>  

                    {!! $histories->links() !!}
              </div>
            </div>
          </div>
          
            <!-- end -->
            </div>
          </div>
        </div>
      </div>

      @include('components.footer')

    </div>
  </div>

 @include('components.scripts')
 
</body>

</html>