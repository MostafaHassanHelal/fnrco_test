<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link rel="stylesheet" href="{{asset('css/blog.css')}}">

<link href="https://fonts.googleapis.com/css?family=Rokkitt" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<!-- ==============================================
	    Hero
	    =============================================== -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<a class="navbar-brand" href="#">My Blog</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		</ul>
		@php
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($url);
		if(isset($parts['query'])){
		parse_str($parts['query'], $query);
		$token= $query['token'];
		}
		@endphp

		<div class="form-inline my-2 my-lg-0">
			@if(strpos($url,'token') === false)
			<input id="login_email" class="form-control mr-sm-2" type="text" placeholder="Email" aria-label="Email" required>
			<input id="login_password" class="form-control mr-sm-2" type="password" placeholder="Password" aria-label="Password" required>
			<button onclick="login()" class="btn btn-outline-success my-2 my-sm-0">Login</button>
			<button onclick="location.href='/register';" class="btn btn-outline-success my-2 my-sm-0">Register</button>
			@else
			<button onclick="logout()" class="btn btn-outline-success my-2 my-sm-0">Logout</button>
			@endif
		</div>

	</div>
</nav>
<section class="hero">
	<div class="container">
		@if(strpos($url,'token') !== false)
		<div class="row">
			<div class="col-lg-6 offset-lg-3">
				<input type="text" style="width: 100%;" id="new_post_title" placeholder="Post Title" required />
				<textarea style="width: 100%;" id="new_post_description" placeholder="Add Description" required></textarea>
				<input type="file" onchange="uploadImage(event)">Add Image</button>
				<button onclick="createPost()">Post</button>
			</div>
		</div>
		@endif
		@foreach($posts as $post)
		<div class="row">
			<div class="col-lg-6 offset-lg-3">
				<div class="cardbox shadow-lg bg-white">
					<div class="cardbox-heading">
						<!-- START dropdown-->
						<div class="dropdown float-right">
							<button onclick="deletePost({{$post->id}})" class="btn btn-flat btn-flat-icon" type="button" aria-expanded="false">
								<em class="fa fa-trash"></em>
							</button>
						</div>
						<!--/ dropdown -->
						<div class="media m-0">
							<div class="d-flex mr-3">
							</div>
							<div class="media-body">
								<p class="m-0">{{$post->user->name}}</p>
								<small><span><i class="icon ion-md-time"></i> {{$post->created_at->diffForHumans()}}</span></small>
								<h2>{{$post->title}}</h2>
								<h5>{{$post->description}}</h5>
							</div>
						</div>
						<!--/ media -->
					</div>
					<!--/ cardbox-heading -->

					<div class="cardbox-item">
						@if(isset($post->albums))
						@foreach($post->albums as $album)
						<img class="img-fluid" src="{{asset('/storage/'.$album->image)}}" alt="Image">
						@endforeach
						@endif
					</div>

					@if(isset($post->tags))
					@foreach($post->tags as $tag)
					<a href="?tag={{$tag->id}}&token={{$token??''}}" target="_blank">{{$tag->title}}</a>
					@endforeach
					@endif

					<!--/ cardbox-item -->
					<div class="cardbox-base">
						<ul class="float-right">
							<li><a><i class="fa fa-comments"></i></a></li>
							<li><a><em class="mr-5">{{$post->comments->count()}}</em></a></li>
						</ul>
						<ul>
							<li><a onclick="makeLike({{$post->id}})"><i class="fa fa-thumbs-up"></i></a></li>
							<li><a><span>{{$post->likes->count()}} likes</span></a></li>
						</ul>
					</div>
					<!--/ cardbox-base -->
					@if(isset($post->comments))
					@foreach($post->comments as $comment)
					<div class="row">
						<span class="col-sm-1 offset-lg-1">
							<a href=""><img style="width: 30px;" class="rounded-circle" src="https://cdn-icons-png.flaticon.com/512/1946/1946429.png" alt="..."></a>
						</span>
						<div class="col-sm-1 ">
							<p class="m-0">{{$comment->user->name}}</p>
						</div>
						<div class="col-lg offset-lg-1">
							<p class="m-0">{{$comment->content}}</p>
						</div>
					</div>
					@endforeach
					@endif

					<div class="cardbox-comments">
						<span class="comment-avatar float-left">
							<a href=""><img class="rounded-circle" src="https://cdn-icons-png.flaticon.com/512/1946/1946429.png" alt="..."></a>
						</span>
						<div class="search">
							<input id="comment_input_{{$post->id}}" placeholder="Write a comment" type="text">
							<button onclick="makeComment(event, {{$post->id}})"><i class="fa fa-send"></i></button>
						</div>
						<!--/. Search -->
					</div>
					<!--/ cardbox-like -->

				</div>
				<!--/ cardbox -->
				<!--/. Search -->
				<!--/ cardbox-like -->

			</div>
			<!--/ cardbox -->
		</div>
		<!--/ col-lg-6 -->

	</div>
	@endforeach
	<!--/ row -->
	</div>
	<!--/ container -->
</section>


<script>
	let imagesLinks = [];

	function makeComment(event, post_id) {
		let commentInput = $(`#comment_input_${post_id}`);
		let comment = commentInput.val();
		if (!comment || comment.trim() === '') {
			return;
		}

		let formData = new FormData();
		formData.append('content', comment);
		formData.append('post_id', post_id);
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/comment",
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					document.location.reload();
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}

	function makeLike(post_id) {
		let formData = new FormData();
		formData.append('post_id', post_id);
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/like",
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					document.location.reload();
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}

	function logout() {
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/logout",
				method: 'post',
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					document.location.href = document.location.origin;
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}


	function deletePost(id) {
		let formData = new FormData();
		formData.append('post_id', id);
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/post",
				method: 'DELETE',
				data: JSON.stringify({
					post_id: id
				}),
				contentType: 'application/json',
				processData: false,
				dataType: 'json',
				success: (result) => {
					document.location.reload();
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}

	function createPost() {
		let postTitle = $("#new_post_title").val();
		let postDescription = $("#new_post_description").val();
		let tags = postDescription.match(/#[a-zA-Z0-9_]+/g);
		let formData = new FormData();
		formData.append("title", postTitle);
		formData.append("description", postDescription);
		if (tags != null) {
			formData.append("tags", tags);
		}
		if (imagesLinks != []) {
			formData.append("images", imagesLinks);
		}
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/posts",
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					document.location.reload();
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}

	function login() {
		let emailInput = $('#login_email');
		let passwordInput = $('#login_password');
		let email = emailInput.val();
		let password = passwordInput.val();
		if (!email || email.trim() === '' || !password || password.trim() === '') {
			return;
		}

		let formData = new FormData();
		formData.append('email', email);
		formData.append('password', password);
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/login",
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					var searchParams = new URLSearchParams(window.location.search)
					searchParams.set("token", result.token);
					var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
					history.pushState(null, '', newRelativePathQuery);
					document.location.reload();
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}

	function uploadImage(event) {
		let image = event.target.files[0];
		if (image == null) {
			return;
		}
		let formData = new FormData();
		const urlParams = new URLSearchParams(window.location.search);
		const token = urlParams.get('token');
		formData.append('image', image);
		$.ajaxSetup({
			headers: {
				'Authorization': `Bearer ${token}`,
			}
		});
		return new Promise((resolve, reject) => {
			$.ajax({
				url: "/api/uploadImage",
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: (result) => {
					imagesLinks.push(result.url);
				},
				error: (error) => {
					reject(error);
				}
			});
		});
	}
</script>