   <div  data-color="blue" class="sidebar-wrapper">
        <div class="logo">
          <a href="/user/profile/" class="simple-text logo-mini">
            <i class="tim-icons icon-single-02"></i>
          </a>
          <a href="/user/profile/" class="simple-text logo-normal">
            {{ Auth::user()->username }}
          </a>
        </div>
        <ul class="nav nav-side">
          @if(Auth::user()->user_role == 3)
          <li>
            <a href="/admin/dashboard">
              <i class="tim-icons icon-atom"></i>
              <p>Admin Panel</p>
            </a>
          </li>
          @elseif(in_array(Auth::user()->user_role,[1,2]))
          <li>
            <a href="/agents/dashboard">
              <i class="tim-icons icon-double-right"></i>
              <p>Agent Panel</p>
            </a>
          </li>
          @elseif(Auth::user()->user_role == 5)
          <li>
            <a href="/verifier/dashboard">
              <i class="tim-icons icon-user-run"></i>
              <p>Verifier Panel</p>
            </a>
          </li>
          @endif

          @if(in_array(Auth::user()->user_role,[3,4]))
          <li>
            <a class="btn-reset-event" href="javascript:void(0)">
              <i class="tim-icons icon-refresh-02"></i>
              <p>Reset Event</p>
            </a>
          </li>
          @endif
          <li>
            <a href="/arena">
              <i class="tim-icons icon-spaceship"></i>
              <p>Arena</p>
            </a>
          </li>
          <li>
            <a href="/user/betting/logs">
              <i class="tim-icons icon-molecule-40"></i>
              <p>My Betting Logs</p>
            </a>
          </li>
          <li>
            <a href="/user/profile">
              <i class="tim-icons icon-single-02"></i>
              <p>My Profile</p>
            </a>
          </li>
          <li>
            <a href="/request/deposit">
              <i class="tim-icons icon-coins"></i>
              <p>Deposit</p>
            </a>
          </li>
          <li>
            <a href="/request/withdraw">
              <i class="tim-icons icon-money-coins"></i>
              <p>Withdraw</p>
            </a>
          </li>
          <li>
            <a href="/user/ask">
              <i class="tim-icons icon-chat-33"></i>
              <p>Customer Service Chat</p>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="modal fade" id="RULES" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Rules">Rules And Guidelines</h5>
            </div>
            <div class="modal-body">
              <p>
                <strong>
                    RULES ON BETTING</br>
                    1. TATLONG LAGPAS HINDI NAGLABAN -DRAW
                   </br>2. PAREHONG MAHINA WALANG LAGLAG -DRAW
                   </br>3. DALAWANG LAGLAG KALABAN AUTO WIN
                   </br>4. ISANG LAGLAG KALABAN DALAWANG LAGPAS AUTO WIN UNG NAKAPAG PAUNANG LAGLAG ANG PANALO
                   </br>5. BINALUTAN AUTOMATIC TALO
                   </br>6. HINDI NA GUMALAW ANG KALABAN NATIRANG MALAKAS ANG PANALO
                   </br>7. TUGIS ISANG SAPOT UNANG MALAGLAG BILANG ISA
                   </br>8. PAREHONG MAY BILANG DALAWANG LAGPAS -DRAW
                   </br>9. NAKA SCORE NG ISA PERO HINDI NA NAG LALAKAD -LOSE
                   </br>10. HINDI NA NAG LALAKAD KAHIT WALANG BILANG -LOSE
                </p>

                <p>
                <strong>RULES ON LOAD</strong></br>
                1. SPIDER FIGHTING IS NOT RESPONSIBLE FOR YOUR CASH-IN/CASH-OUT ON ACCOUNT NUMBERS THAT ARE NOT ON OUR PLATFORM.<br />
                2. WE CAN ONLY CATER TO&nbsp;THOSE CASH-IN/CASH-OUT UNDER OUR ACCOUNT NUMBERS.<br />
                3. WE ARE NOT RESPONSIBLE TO ANY AGREEMENT YOU MADE WITH YOUR AGENT/MASTER AGENT.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
    </div>