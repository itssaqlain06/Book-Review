@extends('layouts.app')

@section('main')
    <div class="container">
        <div class="row my-5">
            <div class="col-md-3">
                <div class="card border-0 shadow-lg">
                    <div class="card-header  text-white">
                        Welcome, {{ Auth::user()->name }}
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if (Auth::user()->image)
                                <img src="{{ asset('uploads/profile/thumb/' . Auth::user()->image) }}"
                                    class="img-fluid rounded-circle" alt="{{ Auth::user()->name }}">
                            @endif
                        </div>
                        <div class="h5 text-center">
                            <strong>{{ Auth::user()->name }}</strong>
                            <p class="h6 mt-2 text-muted">5 Reviews</p>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-lg mt-3">
                    <div class="card-header  text-white">
                        Navigation
                    </div>
                    <div class="card-body sidebar">
                        @include('layouts.sidebar')
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @include('layouts.message')
                <div class="card border-0 shadow">
                    <div class="card border-0 shadow">
                        <div class="card-header  text-white">
                            Books
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('books.create') }}" class="btn btn-primary">Add Book</a>
                                <form action="" method="get">
                                    <div class="d-flex">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Enter keyword" value="{{ Request::get('search') }}">
                                        <button class="btn btn-primary mx-1" type="submit">Search</button>
                                        <a href="{{ route('books.index') }}" class="btn btn-secondary">Clear</a>
                                    </div>
                                </form>
                            </div>
                            <table class="table  table-striped mt-3">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th width="150">Action</th>
                                    </tr>
                                <tbody>
                                    @if ($books->isNotEmpty())
                                        @foreach ($books as $book)
                                            <tr>
                                                <td>{{ $book->title }}</td>
                                                <td>{{ $book->author }}</td>
                                                <td>3.0 (3 Reviews)</td>
                                                <td>
                                                    @if ($book->status == 1)
                                                        <span class="text-success">Published</span>
                                                    @else
                                                        <span class="text-danger">Draft</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-success btn-sm"><i
                                                            class="fa-regular fa-star"></i></a>
                                                    <a href="{{ route('books.edit', $book->id) }}"
                                                        class="btn btn-primary btn-sm"><i
                                                            class="fa-regular fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="#" onclick="deleteBook({{ $book->id }})"
                                                        class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">Books not found</td>
                                        </tr>
                                    @endif
                                </tbody>
                                </thead>
                            </table>

                            {{-- Pagination --}}
                            @if ($books->isNotEmpty())
                                {{ $books->links() }}
                            @endif
                            {{-- <nav aria-label="Page navigation ">
                                <ul class="pagination">
                                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                            </nav> --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        function deleteBook(id) {
            if (confirm("Are you sure you wanna delete this book?")) {
                $.ajax({
                    url: '{{ route('books.destroy') }}',
                    type: "delete",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                        window.location.href = '{{ route('books.index') }}';
                    }
                });
            }
        }
    </script>
@endsection
