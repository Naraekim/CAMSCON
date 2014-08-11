<!DOCTYPE html>
<html lang="{{App::getLocale()}}">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('head_title','Campus Style Icon')</title>

	<!--jQuery UI-->
	<link href="{{asset('packages/jquery-ui-1.11.0-hot-sneaks-full/jquery-ui.min.css')}}" rel="stylesheet" />

	<!-- Bootstrap -->
	<link href="{{asset('packages/bootstrap-3.2.0/css/bootstrap.min.css')}}" rel="stylesheet" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!--Admin Layout styles-->
	<link href="{{asset('admin-assets/layouts/admin-master.css')}}" rel="stylesheet" />

	@yield('head_styles')
	</head>
<body>
<script type="text/javascript">
var User={
	endpoints:{
		loginWithFB:"{{$UserData->endpoints->loginWithFB}}"
	},
	csrf_token:"{{csrf_token()}}",
	status:"{{$UserData->status}}",
	intended:"{{url($UserData->intended)}}",
	loginWithFB:function() {
		$('#loginFormMsg').text('연결된 페이스북 계정으로 로그인을 시도하고 있습니다...');

		//Ajax login
		var data={
			_token:this.csrf_token
		}

		$.post(this.endpoints.loginWithFB,data,function(response) {
			$('#loginFormMsg').text('');
			
			//Basic response validation
			if('type' in response && (response.type=='success' || response.type=='error')) {
				//Pass response to loginResponseHandler()
				User.loginResponseHandler(response);
			}
		},'json');
	}/*loginWithFB()*/,
	loginResponseHandler:function(response) {
		if(response.type=='success') {
			//Handle intended redirection
			document.location.href=this.intended;
		} else if(response.type=='error') {
			//Set alert message
			var alertMsg='알 수 없는 에러가 발생했습니다.';
			switch(response.msg) {
				case 'fb_api_error':
					alertMsg='페이스북 API 장애가 발생했습니다. 잠시 후에 다시 시도해 주세요 :(';
					break;
				case 'fb_validation_error':
					alertMsg='페이스북 로그인 오류가 발생했습니다. 페이스북 로그인을 다시 시도해 주세요 :(';
					break;
				case 'no_user':
					alertMsg='존재하지 않는 사용자 입니다. 우선 가입을 하신 뒤에 관리자에게 권한을 요청하시기 바랍니다 :(';
					break;
			}
			//Launch alert modal
			AdminMaster.alertModal.launch(alertMsg,null,null);
		}
	}/*loginResponseHandler()*/,
	FB:{
		statusChangeCallback:function(response) {
			console.log(response);
			if(response.status==='connected' && User.status=='not_logged_in') {
				//Login to app
				User.loginWithFB();
			}
		}/*statusChangeCallback()*/,
		checkLoginState:function() {
			FB.getLoginStatus(function(response) {
				if(response.status==='connected') {
					User.FB.statusChangeCallback(response);
				} else {
					//Login to facebook
					User.FB.loginToFacebook();
				}
			});
		}/*checkLoginState()*/,
		loginToFacebook:function() {
			FB.login(function(response) {
				if(response.authResponse) {
					User.FB.statusChangeCallback(response);
				} else {
					//Handle cacellation by user
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope:'public_profile,email'});
		}/*loginToFacebook()*/
	}/*FB*/
};

window.fbAsyncInit = function() {
	FB.init({
	appId      : '562011007255630',
	cookie     : true,  // enable cookies to allow the server to access 
	                    // the session
	xfbml      : true,  // parse social plugins on this page
	version    : 'v2.0' // use version 2.0
	});

	// Now that we've initialized the JavaScript SDK, we call 
	// FB.getLoginStatus().  This function gets the state of the
	// person visiting this page and can return one of three states to
	// the callback you provide.  They can be:
	//
	// 1. Logged into your app ('connected')
	// 2. Logged into Facebook, but not your app ('not_authorized')
	// 3. Not logged into Facebook and can't tell if they are logged into
	//    your app or not.
	//
	// These three cases are handled in the callback function.

	FB.getLoginStatus(function(response) {
		User.FB.statusChangeCallback(response);
	});

};

// Load the SDK asynchronously
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ko_KR/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

	<header class="admin-header container">
		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a href="#">대시보드</a></li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					스타일 아이콘 관리 <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#">업로드</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					메타데이터 관리 <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="{{action('BrandsController@showDashboard')}}">패션 브랜드 관리</a></li>
					<li><a href="{{action('CategoriesController@showDashboard')}}">패션 아이템 카테고리 관리</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					회원 체계 관리 <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="{{action('GroupsController@showEditor')}}">회원 조회 및 그룹 관리</a></li>
				</ul>
			</li>
		</ul>
	</header>

	<div class="admin-body container">
		@yield('content')
	</div><!--/.admin-body-->

	<footer class="admin-footer container">
		<div class="admin-footer-content">
			@if(Auth::check())
			<a href="" class="btn btn-default btn-xs admin-logout-btn">로그아웃</a>
			@endif
		</div>
	</footer>

	<!--Confirm Modal-->
	<div id="confirmModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">확인</h4>
				</div>
				<div id="confirmModalBody" class="modal-body"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
					<button type="button" id="confirmModalConfirm" class="btn btn-primary">확인</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!--/Confirm Modal-->

	<!--Alert Modal-->
	<div id="alertModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">알림</h4>
				</div>
				<div id="alertModalBody" class="modal-body"></div>
				<div class="modal-footer">
					<button type="button" id="alertModalConfirm" class="btn btn-primary">확인</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!--/Alert Modal-->

	<!-- jQuery 1.11.1 -->
	<script src="{{asset('packages/jquery-1.11.1/jquery-1.11.1.min.js')}}"></script>
	<!--jQuery UI-->
	<script src="{{asset('packages/jquery-ui-1.11.0-hot-sneaks-full/jquery-ui.min.js')}}"></script>
	<!-- Bootstrap 3.2.0 -->
	<script src="{{asset('packages/bootstrap-3.2.0/js/bootstrap.min.js')}}"></script>
	<!--Verge.js-->
	<script src="{{asset('packages/verge/verge.min.js')}}"></script>
	<script type="text/javascript">
		jQuery.extend(verge);
	</script>

	<!--Admin Layout-->
	<script src="{{asset('admin-assets/layouts/admin-master.js')}}"></script>
	@yield('footer_scripts')
</body>
</html>