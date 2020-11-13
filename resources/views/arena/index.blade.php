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
      <div class="col-sm-12 m-0 p-0">
      
        @if(Auth::user()->activation == 0)
          <div class="alert alert-danger text-primary card ">
              <h5 class="text-primary mb-0 h5-activation">YOUR ACCOUNT IS AWAITING ACTIVATION.</h5> <br> 
              <h5 class="h5-message">PLEASE WAIT A MINUTES OR CONTACT YOUR AGENT FOR ACTIVATION.</h5> </div>
          </div>
        @elseif(Auth::user()->credits < 100 && $user->disabled_viewing)
          <div class="alert alert-danger text-primary card ">
              <h5 class="text-primary mb-0 h5-activation">YOUR CREDITS IS NOT ENOUGH TO ENTER BETTING CONSOLE.</h5> <br> 
              <h5 class="h5-message">MINIMUM CREDITS TO ENTER IS <span class="text-primary">100</span>. TOP UP NOW TO CONTINUE VIEWING THE STREAM</h5> </div>
          </div>
        @else
        <div class="row">

          <div class="col-lg-8 col-md-12">
            <div class="card mb-2">
              <div class="card-header">
                <h6 class="title d-inline">PRIVATE SPIDER FIGHTING</h6>
                <p class="card-category d-inline">300 expected fights</p>
              </div>
              <div class="card-body p-0 pt-3">
                <div class="rounded-bottom">
                  <video id="videoplayer" preload="none" style="height:100%;"  class="" x-webkit-airplay="deny" autoplay playsinline controls webkit-playsinline>
                    <source src="https://d3kiumzfcv5w0b.cloudfront.net/out/v1/c6f1a7bf57be426a90f0829b892b0092/live.m3u8" type="application/x-mpegURL" />
                  </video>
                </div>
              </div>
              @if(in_array(Auth::user()->user_role,[3,4]))

              <div class="card-footer mt-1 text-center">
                <div class="btn-container ml-1 p-1">
                  <button data-type='create' class="btn-admin mt-1">CREATE</button>
                  <button data-type='close' class="btn-admin mt-1">CLOSE</button>
                  <button data-type='open' class="btn-admin mt-1">OPEN</button>
                  <button data-type='lastcall' class="btn-admin mt-1">LAST CALL</button>
                  <button data-type='message' class="btn-admin mt-1">MESSAGE</button>
                  <button data-type='declare' class="btn-admin mt-1">DECLARE</button>
                </div>
              </div>
              @endif
            </div>
          </div>
          

          <div class="col-lg-4 col-md-10">
          <div class="marquee"><p></p></div>
            <div class="card pt-0 mb-2">
              <div class="card-body pt-0 px-0 pb-0 mb-0">
                <div class="table-responsive pb-0">
                  <table class="tbl-betting table mb-0 pb-0">
                    <thead class=" text-primary">
                      <tr>
                        <th>
                          Betting
                        </th>
                        <th>
                          Fight no.
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                        <button class="btn btn-bet @if(!is_null($fight)) {{ $fight->betting_status == 0 ? 'btn-primary' : 'btn-warning' }} @else btn-primary @endif bet-status">
                          @if(!is_null($fight)) 
                            {{ $fight->betting_status == 0 ? 'OPEN' : 'CLOSED' }} 
                          @else 
                            NO BETTING 
                          @endif
                        </button>
                        </td>
                        <td>
                          <span class="fight_no">#{{ $fight->fight_no ?? '' }}</span>
                        </td>
                      </tr>

                      <tr>
                        <td class="meron-bg"><span class="bets">MERON</span></td>
                        <td class="wala-bg"><span class="bets">WALA</span></td>
                      </tr>
                     
                      <tr>
                        <td><span class="t_left total-bets">{{ $bet->left_side_total_bet }}</span><br /> PAYOUT = <span class="payout-left">{{ $bet->left_user_payout }}</span> <br /> <span class="m-left multiplier">{{ $bet->left_side_total_percentage }}</span>
                        </td>
                        <td><span class="t_right total-bets">{{ $bet->right_side_total_bet }}</span><br /> PAYOUT = <span class="payout-right">{{ $bet->right_user_payout }}</span> <br /> <span class="m-right multiplier">{{ $bet->right_side_total_percentage }}</span>
                        </td>
                      </tr>

                      <tr>
                        <td class="bet-here"><button data-type='meron' class="bet-btn-load bet-left bet btn btn-block bet-btn" {{ $bet->cant_bet ? 'disabled' : ''}}><span class="fa fa-plus-circle"></span> BET MERON</button>
                        </td>
                        <td class="bet-here"><button data-type='wala' class="bet-btn-load bet-right bet btn btn-block bet-btn" {{ $bet->cant_bet ? 'disabled' : ''}}><span class="fa fa-plus-circle"></span> BET WALA</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <div class="card rounded-0 mb-1">
                    <div class=" mb-0 pt-0 pr-1">
                      <span class="current-points pull-right">CURRENT POINTS: <span class="points"> {{ Auth::user()->credits }} </span></span>
                      <div class="mt-0 mb-0 input-group form-group">
                        <input type="number" class="ml-1 mb-1 mr-0 form-control mt-1 bet-input" step='0.01' placeholder="Enter your amount here...">
                        <span class="input-group-btn pl-0 pr-0">
                          <button class="form-control btn-clear mt-1">Clear</button>
                        </span>
                      </div>
                      <div class="btn-container ml-1">
                        <button data-amount='100' class="btn-bet-hold mt-1">100</button>
                        <button data-amount='500' class="btn-bet-hold mt-1">500</button>
                        <button data-amount='2000' class="btn-bet-hold mt-1">2000</button>
                        <button data-amount='5000' class="btn-bet-hold mt-1">5000</button>
                        <button data-amount='10000' class="btn-bet-hold mt-1">10000</button>
                        <button data-amount='all-in' class="btn-bet-hold mt-1">ALL IN</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card rounded trend-card mb-2 hidden">
              <div class=" m-2 pt-0 pr-1">
                <span class="current-trends">CURRENT TRENDS &nbsp;<span class="tim-icons icon-cloud-download-93"></span></span>
                <div style="height:150px;" class="trend-div mt-2" data-simplebar id='scrollArea'>
                  <table cellspacing=0 class="trend-table">
                  </table>
                </div>
              </div>
            </div>

            <div class="card rounded-0 history-card hidden">
              <div class=" m-2 pt-0 pr-1"> 
              <span class="current-trends">MY HISTORY &nbsp;<span class="tim-icons icon-coins"></span></span>
                <div class="append_history">
                </div>
              </div>
            </div>

            </div>

          </div>
        </div>
        @endif
      </div>

      @include('components.footer')

    </div>
  </div>

  @include('components.scripts')
 
</body>

</html>