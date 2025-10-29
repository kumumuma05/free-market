@extends('layouts.auth')

@section('css')
    <link rel="stylesheet">
@endsection

@section('content')
    <div class="verify">
        <h2>メール認証を行ってください</h2>
        <p>送付された認証メールを確認し添付されたURLをクリックして認証を行ってください。</p>

        <a href="http://localhost:8025">メールを確認する</a>
    </div>
@endsection
