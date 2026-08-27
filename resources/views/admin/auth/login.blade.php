@extends('layouts.app')

@section('title', '관리자 로그인')

@section('content')
    <div class="login-box">
        <h1>Admin Login</h1>
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="field">
                <label for="id">아이디</label>
                <input type="text" name="id" id="id" value="{{ old('id') }}" autofocus>
            </div>
            <div class="field">
                <label for="password">비밀번호</label>
                <input type="password" name="password" id="password">
            </div>
            <button type="submit" class="btn btn-accent" style="width:100%">로그인</button>
        </form>
    </div>
@endsection
