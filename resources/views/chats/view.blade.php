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
      <div class="content" id="content">
        <div class="row">
        <!-- start -->

        
    <div class="col-md-12 rounded">

      <h4>{{ $subjects->subject_name }}</h4>

        <!-- Chat Box-->
      <div class="px-4 py-5 chat-box rounded" id="chat-box">

      
      </div>

      <!-- Typing area -->
      <form class="bg-light send-chat">
        <div class="input-group">
          <input type="text" placeholder="Type a message" name="message" aria-describedby="button-addon2" class="input_chat_message form-control text-dark rounded-0 border-0 py-4 bg-light" required>
          <div class="input-group-append border-0">
            <button id="button-addon2 border-0" type="submit" class="btn btn-link"> <i class=" border-0 fa fa-paper-plane"></i></button>
          </div>
        </div>
        </form>

        </div>

          <!-- end -->
      </div>
    </div>
  </div>
</div>

@include('components.footer')

</div>
</div>

<script>

  //recommended for getting the subject_id
  const subject_id = {{ $subjects->id }}

</script>

 @include('components.scripts')
 
</body>

</html>