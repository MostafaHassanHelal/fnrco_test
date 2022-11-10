<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link rel="stylesheet" href="{{asset('css/blog.css')}}">

<link href="https://fonts.googleapis.com/css?family=Rokkitt" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<section class="hero">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 offset-lg-3">
        <input id="register_name" class="form-control mr-sm-2" type="text" placeholder="Name" aria-label="Name" required>
        <input id="register_email" class="form-control mr-sm-2" type="text" placeholder="Email" aria-label="Email" required>
        <input id="register_password" class="form-control mr-sm-2" type="password" placeholder="Password" aria-label="Password" required>
        <button onclick="register()" class="btn btn-outline-success my-2 my-sm-0">Register</button>
      </div>
    </div>
    <!--/ row -->
  </div>
  <!--/ container -->
</section>


<script>
  function register() {
    let name = $('#register_name').val();
    let email = $('#register_email').val();
    let password = $('#register_password').val();
    if (!email || email.trim() === '' || !password || password.trim() === '' || !name || name.trim() === '') {
      return;
    }

    let formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    return new Promise((resolve, reject) => {
      $.ajax({
        url: "/api/register",
        method: 'post',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: (result) => {
          console.log(window.location.host);
          document.location.href = window.location.origin + "/?token=" + result.token;
        },
        error: (error) => {
          reject(error);
        }
      });
    });
  }
</script>