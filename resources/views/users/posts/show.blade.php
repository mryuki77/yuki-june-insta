@extends('layouts.app')

@section('title', 'Show Post')

@section('content')

<style>
    .col-4{
        overflow-y:scroll;
    }

    .card-body{
        position:absolute;
        top:65px;
    }
</style>

    <div class="row border shadow">
        <div class="col p-0 border-end">
            <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="w-100">
        </div>
        <div class="col-4 px-0 bg-white">
            <div class="card bord-0">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a href="{{route('profile.show',$post->user->id)}}">
                                @if ($post->user->avatar)
                                   <img src="{{$post->user->avatar}}" alt="{{ $post->user->name }}" class="rounded-circle avatar-sm"> 
                                @else
                                    <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col ps-0">
                            <a href="{{route('profile.show',$post->user->id)}}" class="text-decoration-none text-dark">{{ $post->user->name }}</a>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                
                                {{-- If you are the owner of the post, you can edit and delete this post --}}
                                @if ( Auth::user()->id === $post->user->id )
                                    <div class="dropdown-menu">
                                        <a href="{{route('post.edit',$post->id)}}" class="dropdown-item">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </a>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete-post-{{ $post->id }}">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                
                                    </div>
                                    @include('users.posts.contents.modals.delete')
                                @else
                                {{-- If you are NOT THE OWNER of the post, then show the Unfollow button. To be discussed later on --}}
                                    <div class="dropdown-menu">

                                        @if ($post->user->isFollowed())
                                            <form action="{{route('follow.destroy',$post->user->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-secondary">following</button>
                                            </form>
                                        @else
                                            <form action="{{route('follow.store',$post->user->id)}}" method="post">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">follow</button>
                                            </form>
                                        @endif
                                        
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body w-100">
                    {{-- heart icon + no of likes + categories --}}
                    <div class="row align-items-center">
                        <div class="col-auto">

                            @if ($post->isLiked())
                            <form action="{{route('like.destroy',$post->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm shadow-none p-0"><i class="fa-solid fa-heart text-danger"></i></button>
                            </form>
                            @else
                            <form action="{{route('like.store',$post->id)}}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm shadow-none p-0"><i class="fa-regular fa-heart"></i></button>
                            </form>
                            @endif
                            
                        </div>
                        <div class="col-auto px-0">
                            <span>{{$post->likes->count()}}</span>
                        </div>
                        <div class="col text-end">
                            @forelse ($post->categoryPost as $category_post)
                                <span class="badge bg-secondary bg-opacity-50">
                                    {{$category_post->category->name}}
                                </span>
                            @empty
                                <div class="badge bg-dark text-wrap">Uncategorized</div>
                            @endforelse
                            {{-- @foreach ($post->categoryPost as $category_post)
                                <div class="badge bg-secondary bg-opacity-50">
                                    {{ $category_post->category->name }}
                                </div>
                            @endforeach --}}
                        </div>
                    </div>

                    {{-- owner + description --}}
                    <a href="{{route('profile.show',$post->user->id)}}" class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                    &nbsp;
                    <p class="d-inline fw-light">{{ $post->description }}</p>
                    &nbsp;
                    <p class="text-danger small">Posted on {{ $post->created_at->diffForHumans() }}</p>
                    <p class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($post->created_at)) }}</p>

                    {{-- strtotime() --> is a builtin function in PHP use to convert the time and date into human readable format  --}}


                    {{-- Comments Section --}}
                    <div class="mt-4">
                    
                        <form action="{{route('comment.store',$post->id)}}" method="post">
                            @csrf
                    
                            <div class="input-group">
                                {{--The "$post->id" is the post being commented. --}}
                                <textarea name="comment_body{{$post->id}}" id="" rows="1" class="form-control form-control-sm" placeholder="Add a comment...">{{old('comment_body'.$post->id)}}</textarea>
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Post</button>
                            </div>
                            @error('comment_body'.$post->id)
                                <div class="text-danger small">{{$message}}</div>
                            @enderror
                        </form>

                        {{--Show all the comments here--}}
                        @if ($post->comments->isNotEmpty())
                            <ul class="list-group mt-2">
                                @foreach ($post->comments as $comment)
                                    <li class="list-group-item border-0 p-0 mb-2">
                                        <a href="{{route('profile.show',$comment->user->id)}}" class="text-decoration-none text-dark fw-bold">{{$comment->user->name}}</a>
                                        &nbsp;
                                        <p class="d-inline fw-light">{{$comment->body}}</p>

                                        <form action="{{route('comment.destroy',$comment->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <span class="text-uppercase text-muted xsmall">{{date('M d,Y',strtotime($comment->created_at))}}</span>

                                            {{--If the auth user is the OWNER OF THE COMMENT,show the a delete button--}}
                                            @if (Auth::user()->id===$comment->user->id)
                                                &middot;
                                                <button type="submit" class="border-0 bg-transparent text-danger p-0 xsmall">Delete</button> 
                                            @endif
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection