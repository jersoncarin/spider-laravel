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
            <div class="card">
              <div class="card-header">
                <h5 class="title">
                  @if($req_type == 'deposit')
                    {{ 'Request Deposit'}}
                    <a href="/request/{{ $req_type }}/history" class="btn btn-primary pull-right py-1 px-2">{{ ucfirst($req_type) }} History</a>
                  @elseif($req_type == 'withdraw')
                    {{ 'Request Withdraw'}}
                    <a href="/request/{{ $req_type }}/history" class="btn btn-primary pull-right py-1 px-2">{{ ucfirst($req_type) }} History</a>
                  @else
                    {{ 'No Request Defined '}}
                  @endif

                </h5>
                
              </div>

              <div class="card-body">
                <form method="POST" action="/request/submit" enctype="multipart/form-data">
                  {{ csrf_field() }}

                  <input type="hidden" name="req_type" value="{{ $req_type }}">

                   @if ($errors->any())
                     <div role="alert" class="alert {{ strpos($errors->first('message'),'Failed') !== false ? 'alert-danger' : 'alert-success' }}">
                        @foreach ($errors->all() as $error)
                           {!! $error . '<br>' !!}
                        @endforeach
                     </div>
                     @endif

                    @if($req_type == 'deposit')

                    
                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Amount</label>
                        <input type="number" class="form-control" name="amount" placeholder="Enter your amount" required>
                      </div>
                    </div>
                 
                   <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Sender Number</label>
                        <input type="number" class="form-control" name="sender_number" placeholder="Enter your phone number" required>
                      </div>
                    </div>

                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Receiver Number</label>
                        <select class="form-control" name='reciever_number' required>
                          <option selected disabled value="">--Select receiver number--</option>
                          @forelse($accounts as $account)
                            <option value="{{ $account->account_number }} ">{{ $account->account_number . "($account->account_name)" }}</option>
                          @empty
                              
                          @endforelse
                        </select>
                      </div>
                    </div>

                   <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Referrence Number</label>
                        <input type="number" class="form-control" name="reference_number" placeholder="Paste your reference number">
                      </div>
                    </div>
                 
                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="screenshot">Attached Screenshot</label>
                        <input type="file" class="form-control" name="screenshot" accept="image/x-png,image/gif,image/jpeg" required id="screenshot">
                      </div>
                    </div>

                    @elseif($req_type == 'withdraw')

                    
                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Amount</label>
                        <input type="number" class="form-control" name="amount" placeholder="Enter your amount" required>
                      </div>
                    </div>

                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Account Name</label>
                        <input type="text" class="form-control" name="account_name" placeholder="Enter your account name" required>
                      </div>
                    </div>
                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Account Number</label>
                        <input type="number" class="form-control" name="account_number" placeholder="Enter your account number" required>
                      </div>
                    </div>
                    <div class="col-md-12 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Contact Number</label>
                        <input type="number" class="form-control" name="contact_number" placeholder="Enter your phone number (+63)" required>
                      </div>
                    </div>

                    @endif

                  <button type="submit" class="btn btn-fill btn-primary" {{ !in_array($req_type,['deposit','withdraw']) ? 'disabled' : '' }}>Submit</button>
                </form>
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