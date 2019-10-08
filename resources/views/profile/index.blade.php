<form method="POST" action="/profile" enctype="multipart/form-data" >
    {{ csrf_field() }}
    <input type="file" name="photo">
    <input type="submit">
</form>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($is_image)
    <figure>
        <img src="/storage/profile_images/{{ Auth::id() }}.jpg" width="100px" height="100px">
        <figcaption>現在のプロフィール画像</figcaption>
    </figure>
@endif