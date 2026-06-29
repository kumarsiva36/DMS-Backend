<!DOCTYPE html>
<html>
   <head>
        <title>Add New Permission</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
   </head>
   <body>
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <div class="panel panel-default credit-card-box">
                  <div class="panel-heading" >
                     <div class="row">
                        <h3>Add Permissons to All Roles</h3>
                     </div>
                  </div>
                  <br/>
                  <br/>
                  <form method="POST" action="{{ route('addPermissionUsers') }}">
                    {{ csrf_field() }}
                    <div class="form-row row">
                        <div class="col-lg-12">
                            <label for="perm_id"> Permission ID </label>
                            <input type="number" class="form-control" name="perm_id" placeholder="Enter Permission ID" value="0">
                            <br/>
                        </div>
                        <div class="col-lg-3">
                            <button class="btn btn-primary btn-lg btn-block" type="submit">Enter</button>
                        </div>
                    </div>
                  </form>
                  <br>
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <p>{{ Session::get('success') }}</p><br>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @elseif(Session::has('failure'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <p>{{ Session::get('failure') }}</p><br>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
