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
            <button class="btn btn-primary pull-right mt-1 mb-4 add-subject" data-toggle="modal" data-target="#add-subject" >Add Subject</button>

            @forelse($subjects as $subject)

            <div class="card">
              <div class="card-body">
                <h5 class="card-title display-4">{{ $subject->subject_name }}</h5>
                <p class="card-text">Has Reply : {{ $subject->hasReply == 0 ? 'No' : 'Yes' }}</p>
                <p class="card-text">Status : {{ $subject->status == 0 ? 'Open' : 'Closed' }}</p>
                <a href="/user/ask/view/{{ $subject->id }}" class="btn btn-primary {{ $subject->status == 0 ? '' : 'disabled' }} ">View conversation</a>
              </div>
            </div>

            @empty
            @endforelse

            {{ $subjects->links() }}

        </div>

        <div id="add-subject" class="modal fade" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content text-dark">
              <div class="modal-header">
                <h5 class="modal-title">Add Subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-dark">

              <form method="POST" action="/user/ask/create">
                {{ csrf_field() }}
                <div class="form-group">
                  <label for="subject">Subject Name</label>
                  <input type="text" class="form-control text-dark" name="subject_name" id="subject" aria-describedby="subject" placeholder="Enter subject name" required>
                  <small id="subject" class="form-text text-muted">Enter your subject name pointed by your issue's.</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </form>

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