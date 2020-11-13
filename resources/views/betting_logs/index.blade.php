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
                <h4 class="card-title"> My Betting Logs</h4>
              </div>
              <hr class="mt-0">
              <div class="card-body">
                  
                <div class="table-responsive mb-2">
                    <table class='table table-striped'>
                        <thead class="text-primary">
                        <tr>
                            <th class="text-center">Fight</th>
                            <th class="text-center">Side</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Bet</th>
                            <th class="text-center">Balance</th>
                            <th class="text-center">Date</th>
                        </tr>
                        </thead>
                        <tbody>

                            @forelse($logs as $log)

                                <tr>
                                    <td>{{ sprintf('#%d', $log->fight_no) }}</td>
                                    <td>{{ ucfirst($log->side) }}</td>
                                    <td>{{ ucfirst($log->action) }}</td>
                                    <td>{{ $log->amount }}</td>
                                    <td>{{ $log->bet }}</td>
                                    <td>{{ $log->balance }}</td>
                                    <td>{{ date('F j Y h:i A', strtotime($log->logs_date)) }}</td>
                                </tr>

                            @empty

                                <p>No Logs found!</p>

                            @endforelse

                        </tbody>
                     </table>
                    </div>  

                    {!! $logs->links() !!}
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