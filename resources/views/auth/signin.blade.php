@include('components.header')

<body>
   <div class="container vh-100">
      <div class="row align-items-center h-100">
         <div class="header-mobile__logo-img col-md-6 text-center justify-content-center align-self-center">
            <img class="logo-img" src="/frontend/static/images/logo-sm.png" alt="logo">
         </div>
         <div class="col-md-6 justify-content-center align-self-center pr-10">
            <div class="card">
               <div class="card-header auth-header">
                  SIGN IN TO YOUR ACCOUNT
               </div>
               <hr>
               <div class="card-body">
                  <form method="POST" action="/signin" autocomplete="off">
                     {{ csrf_field() }}
                  
                     @if ($errors->any())
                     <div role="alert" class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                           {!! $error . '<br>' !!}
                        @endforeach
                     </div>
                     @endif

                     <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Enter your username" required>
                        <small id="username" class="form-text text-muted">We'll never share your username with anyone else.</small>
                     </div>
                     <div class="form-group mb-2">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Password" required>
                     </div>
                     <button type="submit" name="signin" class="btn btn-primary btn-block">SIGN IN NOW</button>
                  </form>
                  <div class="form-group mt-2">
                    <span>Dont have an account yet? <a href="/signup">Sign Up Now</a></span>
                  </div>
                  <h5 class="text-danger m-0">SPIDER FIGHTING | ALL RIGHTS RESERVED {{ date('Y') }}.</h5>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>