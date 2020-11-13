@include('components.header')

<body>
   <div class="container vh-100">
      <div class="row align-items-center h-100">
         <div class="header-mobile__logo-img col-md-6 text-center justify-content-center align-self-center">
            <img class="logo-img" src="/frontend/static/images/logo-sm.png" alt="logo">
         </div>
         <div class="col-md-6 justify-content-center align-self-center">
            <div class="card">
               <div class="card-header auth-header">
                  SIGN UP TO YOUR ACCOUNT
               </div>
               <hr>
               <div class="card-body">
                  <form method="POST" action="/signup/complete" autocomplete="off">
                     {{ csrf_field() }}

                     @if ($errors->any())
                     <div role="alert" class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                           {!! $error . '<br>' !!}
                        @endforeach
                     </div>
                     @endif

                     <input type="hidden" name="_referral_token" value='{{ $referral_code }}' required>

                     <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" maxlength="8" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Enter your username" required>
                        <small id="username" class="form-text text-muted">We'll never share your username with anyone else.</small>
                     </div>
                     <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="number" maxlength="12" class="form-control" id="phone" name="phone_number" placeholder="Enter your phone number (+63)" required>
                     </div>
                     <div class="form-group mb-2">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" min='4' id="password" name="password" placeholder="Enter your Password" required>
                     </div>
                     <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="term" required>
                        <label class="form-check-label" for="term">I Agree to <a href="#" data-toggle="modal" data-target="#terms_conditions">Terms and Conditions</a></label>
                     </div>
                     <button type="submit" name="signin" class="btn btn-primary btn-block">SIGN UP NOW</button>
                  </form>
                  <div class="form-group mt-2">
                    <span>Already have account? <a href="/">Sign In Now</a></span>
                  </div>
                  <h5 class="text-danger m-0">SPIDER FIGHTING | ALL RIGHTS RESERVED {{ date('Y') }}.</h5>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="modal fade" id="terms_conditions" tabindex="-1" role="dialog" aria-labelledby="madpk" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal">Terms and Conditions</h5>
            </div>
            <div class="modal-body">
               <p><strong>You hereby agree that we will charge 15% for every fight and this will serve as your donation to spider protection and development.</strong></p>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Dummy Builds -->
   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
</body>