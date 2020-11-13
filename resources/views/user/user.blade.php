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

        
        <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5 class="title">Edit Profile</h5>
              </div>
              <div class="card-body">
                <form method="POST" action="/user/profile/update">
                  {{ csrf_field() }}

                   @if ($errors->any())
                     <div role="alert" class="alert {{ strpos($errors->first('message'),'Failed') !== false ? 'alert-danger' : 'alert-success' }}">
                        @foreach ($errors->all() as $error)
                           {!! $error . '<br>' !!}
                        @endforeach
                     </div>
                     @endif

                  <div class="row">
                    <div class="col-md-6 pr-md-1">
                      <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" placeholder="Username" value="{{ Auth::user()->username }}" disabled>
                      </div>
                    </div>
                    <div class="col-md-6 pl-md-1">
                      <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="number" class="form-control" placeholder="{{ Auth::user()->phone_number }}" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control"  name="password" placeholder="Leave empty (No changes)">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 pr-md-1">
                      <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ $user->first_name ?? ''}}">
                      </div>
                    </div>
                    <div class="col-md-6 pl-md-1">
                      <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control"  name="last_name" placeholder="Last Name" value="{{ $user->last_name ?? ''}}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control"  name="address" placeholder="Your Address" value="{{ $user->address ?? ''}}">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4 pr-md-1">
                      <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" placeholder="City" value="{{ $user->city ?? ''}}">
                      </div>
                    </div>
                    <div class="col-md-4 px-md-1">
                      <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control"  name="country" placeholder="Country" value="{{ $user->country ?? ''}}">
                      </div>
                    </div>
                    <div class="col-md-4 pl-md-1">
                      <div class="form-group">
                        <label>Postal Code</label>
                        <input type="number" class="form-control"  name="zipcode" placeholder="ZIP Code" value="{{ $user->zipcode ?? ''}}">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Credits</label>
                        <input type="number" class="form-control" disabled value="{{ Auth::user()->credits }}">
                      </div>
                    </div>
                  </div>

                  @if(!empty($referral))
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Referral Link</label>
                        <input type="text" class="form-control" disabled value="{{ url('/signup/?r=' . $referral->code ) }}">
                      </div>
                    </div>
                  </div>
                  @endif

                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label>About Me</label>
                        <textarea rows="4" cols="80" class="form-control" name="bio" placeholder="Tell more about yourself" value="">{{ $user->bio ?? ''}}</textarea>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-fill btn-primary">Update</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-user">
              <div class="card-body">
                <p class="card-text">
                  <div class="author">
                    <div class="block block-one"></div>
                    <div class="block block-two"></div>
                    <div class="block block-three"></div>
                    <div class="block block-four"></div>
                    <a href="javascript:void(0)">
                      <img class="avatar" src="/frontend/static/images/default-avatar.png" alt="Profile Image">
                      <h5 class="title">
                          @if( is_null($user) OR (empty($user->first_name) && empty($user->last_name)))
                            {{ '[Full name is not set]'}}
                          @else
                            {{ $user->first_name . ' ' . $user->last_name }}
                          @endif
                        </h5>
                    </a>
                    <p class="description">
                      @if(Auth::user()->user_role == 0)
                        {{ 'Normal Player'}}
                      @elseif(Auth::user()->user_role == 1)
                        {{ 'Master Agent'}}
                      @elseif(Auth::user()->user_role == 2)
                        {{ 'Agent'}}
                      @elseif(Auth::user()->user_role == 3)
                        {{ 'Super Admin'}}
                      @elseif(Auth::user()->user_role == 4)
                        {{ 'Game Master'}}
                      @elseif(Auth::user()->user_role == 5)
                        {{ 'Verifier'}}
                      @endif
                    </p>
                  </div>
                </p>
                <div class="card-description text-center">
                    {{ $user->bio ?? ''}}
                </div>
              </div>
              <div class="card-footer">
                <div class="button-container">
                @if(!empty($referral))
                  <a href="{{ url('/signup/?r=' . $referral->code ) }}" class="btn btn-icon btn-round btn-facebook" alt="Click your referral link" target='_blank'>
                    <i class="fa fa-link"></i>
                  </a>
                @endif
                </div>
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