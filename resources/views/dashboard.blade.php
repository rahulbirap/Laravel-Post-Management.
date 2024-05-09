@extends('layouts.app')
<style>

body {
    font-family: 'Arial', sans-serif; 
    background-color: #f4f4f4;
    color: #333; 
    margin: 0;
    padding: 0;
    line-height: 1.6;
}

.navbar, .sidenav {
    background-color: #333;
    color: white; 
}

.sidenav {
    padding: 15px; 
    height: 100%; 
}

.sidenav h4 {
    padding-top: 15px; 
    border-bottom: 1px solid gray; 
    padding-bottom: 15px; 
}

.sidenav ul {
    list-style: none; 
    padding: 0;
}

.sidenav ul li {
    padding: 10px; 
    border-bottom: 1px solid #ddd; 
}

@media screen and (max-width: 767px) {
    .sidenav, .row.content {
        height: auto;
        padding: 15px;
    }
}


.row.content {
    padding: 20px; 
    min-height: 600px; 
    background-color: white;
    border-radius: 8px; 
    box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
}

.table {
    width: 100%;
    margin-bottom: 20px;
    border-collapse: collapse; 
}

.table th, .table td {
    border: 1px solid #ddd; 
    padding: 12px; 
}

.table th {
    background-color: #f9f9f9; 
}

.table-hover tbody tr:hover {
    background-color: #f1f1f1; 
}

.button, .btn {
    background-color: #007bff; 
    color: white;
    padding: 9px 15px;
    border: none;
    border-radius: 5px; 
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn:hover {
    background-color: #0056b3; 
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px; 
}


footer {
    text-align: center;
    width: 100%;
    position: fixed;
    bottom: 0;
    padding: 10px 0;
}

.close {
    color: white;
    opacity: 1; 
}
</style>
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row content">
        <div class="col-sm-3 sidenav">
            <h4>Welcome {{ Auth::user()->name }}</h4>
            <ul class="list-group">
                <li class="list-group-item">Dashboard</li>
            </ul>
        </div>

        <div class="col-sm-9">
            <div class="buttongroup" style="float:right">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Add New Post</button>
            </div>

            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Post Details</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                           
                            <form action="" method="" id="post_form" enctype="multipart/form-data">
                                <input type="hidden" id="post_id" name="id">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Title" />
                                    <span class="text-danger" id="error-title"></span>
                                </div>
                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea name="content" id="content" class="form-control" placeholder="Content"></textarea>
                                    <span class="text-danger" id="error-content"></span>
                                </div>
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                    <span class="text-danger" id="error-image"></span>
                                </div>
                                <div id="current_image_container" style="display:none">
                                <img id="current_image"  class="img-fluid" alt="Current Image">
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <input type="submit" class="btn btn-primary add_new_post" value="Add" />
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="container">
                    <h2>Posts List</h2>
                    <table class="table table-hover postslist">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @isset($posts)
                            @php
                                $currentPage = $posts->currentPage(); // Get current page from paginator
                                $perPage = $posts->perPage(); // Get the number of items per page
                                $counter = ($currentPage - 1) * $perPage; // Calculate the offset
                            @endphp
                                @forelse($posts as $index => $post)
                                <tr>
                                    <td>{{$counter + $index + 1 }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ $post->content}}</td>
                                    <td>
                                        <a href="#" id="edit_post" data-id="{{ $post->id }}"><i class="fa fa-edit"></i></a>
                                        <a href="#" id="delete_post" data-id="{{ $post->id }}"><i class="fa fa-trash-o"></i></a>
                                    </td> 
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No posts available.</td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center;">No posts available.</td>
                                </tr>
                            @endisset
                        </tbody>
                    </table>
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {

    $(document).on('click','.add_new_post',function(e){
        e.preventDefault();
        var formData = new FormData($('#post_form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('savepost') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
          
                    $(".postslist tbody").html(result);
                    $("#post_form").trigger("reset");
                    $('#myModal').modal('hide');
                    swal("Good job!", "Post Saved Successfully!", "success");  

            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                $('#error-title, #error-content, #error-image').text('');
                if (errors.title) {
                    $('#error-title').text(errors.title[0]);
                }
                if (errors.content) {
                    $('#error-content').text(errors.content[0]);
                }
                if (errors.image) {
                    $('#error-image').text(errors.image[0]);
                }
            }
        });
    });

    $(document).on('click', '#edit_post', function() {
        var id = $(this).data('id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "/edit_post/" + id,
            type: 'GET',
            success: function(response) {
                $('#post_id').val(response.id);
                $('#title').val(response.title);
                $('#content').val(response.content);

                if (response.image) {
                $('#current_image').attr('src', "{{ asset('images/') }}/" + response.image);
                $('#current_image_container').show();
            } else {
                $('#current_image_container').hide();
            }

                $('.add_new_post').val('Update');
                $('#myModal').modal('show');
                
            }
        });
    });

    $(document).on('click', '#delete_post', function() {
    var id = $(this).data('id');

    swal({  
        title: "Confirm Deletion",  
        text: "Are you sure you want to delete this post?",  
        icon: "warning",  
        showCancelButton: true,  
        confirmButtonColor: '#3085d6',  
        cancelButtonColor: '#d33',  
        confirmButtonText: "Yes, delete it!",  
        cancelButtonText: "No, cancel!",  
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/delete_post/" + id,
                type: 'DELETE',
                success: function(result) {
                    swal(
                        "Deleted!",
                        "Your post has been deleted.",
                        "success"
                    );
                    location.reload();  
                },
                error: function(xhr) {
                    swal(
                        "Error",
                        "There was a problem deleting your post.",
                        "error"
                    );
                }
            });
        } else if (result.dismiss === swal.DismissReason.cancel) {
            swal(
                "Cancelled",
                "Your post is safe ",
                "error"
            );
        }
    });
});

});
</script>

@endsection
