<!-- resources/views/chat/edit.blade.php -->
@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
  <section class="show">
    <div class="show__contents">
      <div class="show__heading">
        <div class="show__headingInfo">
          <h2 class="show__headingTitle">メッセージの編集</h2>
        </div>
      </div>

      <div class="show__productChat">
        <form action="{{ route('chat.edit', $message->id) }}" method="POST">
          @csrf
          @method('POST')
          
          <textarea name="message" class="show__productChatBottomInput">{{ old('message', $message->message) }}</textarea>

          <button type="submit" class="show__productChatBottomBtn">更新</button>
        </form>
      </div>
    </div>
  </section>
@endsection
